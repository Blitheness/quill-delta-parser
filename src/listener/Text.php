<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\Lexer;
use nadar\quill\BlockListener;

class Text extends BlockListener
{
    public function priority(): int
    {
        return self::PRIORITY_GARBAGE_COLLECTOR;
    }

    public function process(Line $line)
    {
        if (!$line->isDone()) {
            $this->pick($line);
        }
    }

    public function render(Lexer $lexer)
    {
        $isOpen = false;
        foreach ($this->picks() as $pick) {
            if (!$pick->line->isDone() && !$pick->line->hasAttribute()) {

                $pick->line->setDone();

                $next = $pick->line->next();
                $prev = $pick->line->previous();
                
                $output = [];

                // wenn previous ist inline und das tag bereits göffnet. Am schluss schliesen.
                // aber nur wenn diese linie leer ist.
                if ($isOpen && ($prev && $prev->getIsInline()) && $pick->line->isEmpty()) {
                    $isOpen = $this->output($output, '</p>', false);
                }
                
                // if its close - we just open tag paragraph as we have a line here!
                if (!$isOpen) {
                    $isOpen = $this->output($output, '<p>', true);
                }

                // write the actuall content of the element into the output
                $output[] = $pick->line->isEmpty() ? '<br>' : $pick->line->renderPrepend() . $pick->line->input;

                // if its open and we have a next element, and the next element is not an inline, we close!
                if ($isOpen && ($next && !$next->getIsInline())) {
                    $isOpen = $this->output($output, '</p>', false);

                // if its open and we dont have a next element, its the end of the document! lets close this damn paragraph.
                } elseif ($isOpen && !$next) {
                    $isOpen = $this->output($output, '</p>', false);

                // its open, but the previous element was already an inline element, so maybe we should close and the next element
                // will take care of the "situation". But only if this current line also had an end new line element, otherwise
                // repeated inline elements will close
                } elseif ($isOpen && ($prev && $prev->getIsInline()) && $pick->line->hadEndNewline) {
                    $isOpen = $this->output($output, '</p>', false);
            
                // If this element is empty we should maybe directly close and reopen this paragraph as it could be an empty line with
                // a next elmenet
                } elseif ($pick->line->isEmpty() && $next) {
                    $isOpen = $this->output($output, '</p><p>', true);
                
                // if its open, and it had an end newline, lets close
                } elseif ($isOpen && $pick->line->hadEndNewline) {
                    $isOpen = $this->output($output, '</p>', false);   
                }
                
                // we have a next element and the next elmenet is inline and its not open, open ...!
                if ($next && $next->getIsInline() && !$isOpen) {
                    $isOpen = $this->output($output, '<p>', true);
                }

                $pick->line->output = implode("", $output);
            }
        }
    }

    protected function output(&$output, $tag, $openState)
    {
        $output[] = $tag;
        return $openState;
    }
}
