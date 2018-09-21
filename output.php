<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use nadar\quill\Parser;
use nadar\quill\Lexer;

require 'vendor/autoload.php';

$json = isset($_POST['quill-editor-input']) ? $_POST['quill-editor-input'] : '{}';

$lex = new Lexer($json);
$lex->initBuiltInListeners();
$html = $lex->render();
?>
<!DOCTYPE html>
<html lang="en">
<head>

<!-- Include Quill stylesheet -->
<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">
<!-- Include the Quill library -->
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>


<form method="post" class="demo">
    <input type="hidden" id="quill-editor-input" name="quill-editor-input" />
    <div id="editor" style="height:100px;">
    <p>intro</p>
    <h1>heading</h1>
    <p>paragraph <b>fett</b>!</p>
    <ul>
        <li>elmn 1</li>
        <li>elmn 2</li>
    </ul>
    <p>text</p>
    <ul>
        <li>elmn <b>bold</b> a</li>
        <li>elmn b</li>
    </ul>
    </div>
    <input type="submit" name="submit" value="Parse" class="btn btn-primary" />
</form>
<!-- OUTPUT TESTER-->
<div style="border:1px solid blue; padding:20px; margin-top:20px;"><pre>
<?= $json; ?>
</pre></div>
<?php foreach ($lex->getLines() as $line): ?>
<ul>
    <li>
        Key: <?= $line->row; ?><br />
        Input: <span style="color:green;">: <?= htmlentities($line->input, ENT_QUOTES); ?></span><br />
        Output: <?= htmlentities($line->output, ENT_QUOTES); ?><br />
        <?php if ($line->attributes): ?>
        <b>Attributes: <?= implode(",", $line->attributes); ?></b>
        <?php endif; ?>
    </li>
</ul>
<?php endforeach; ?>
<div style="border:1px solid red; padding:20px; margin-top:20px;"><pre>
<?php var_dump($html); ?>
</pre></div>
<div style="border:1px solid green; padding:20px; margin-top:20px;">
<?= htmlentities($html, ENT_QUOTES); ?>
</div>

<!-- Initialize Quill editor -->
<script>
  var editor = new Quill('#editor', {
    modules: {  toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['link'],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['image', 'video'],
                ['clean']
            ] },
    theme: 'snow'
  });
  $('form.demo').submit(function() {
        $('#quill-editor-input').val(JSON.stringify(editor.getContents()));
        return true;
    });
</script>
</body>
</html>