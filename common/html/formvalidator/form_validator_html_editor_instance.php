<?php
require_once dirname(__FILE__) . '/../../global.inc.php';

// Getting some properties from the Ajax post
$name = Request :: post('name');
$label = Request :: post('label');
$options = json_decode(Request :: post('options'), true);
$attributes = json_decode(Request :: post('attributes'), true);

$html_editor = FormValidatorHtmlEditor :: factory(LocalSetting :: get('html_editor'), $name, $label, false, $options, $attributes);

echo $html_editor->render();
?>