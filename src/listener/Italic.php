<?php

namespace nadar\quill\listener;

use nadar\quill\Line;
use nadar\quill\InlineListener;

class Italic extends InlineListener
{
    public function process(Line $line)
    {
        if ($line->getAttribute('italic')) {
            $this->updateInput($line, '<em>' . $line->input . '</em>');
        }
    }
}
