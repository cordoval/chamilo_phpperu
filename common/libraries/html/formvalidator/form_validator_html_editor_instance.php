<?php
require_once dirname(__FILE__) . '/../../global.inc.php';

// Getting some properties from the Ajax post
$name = Request :: post('name');
$label = Request :: post('label');

$options = Request :: post('options');
$options = str_replace('\"', '"', $options); 
$options = json_decode($options, true);

$attributes = Request :: post('attributes');
$attributes = str_replace('\"', '"', $attributes); 
$attributes = json_decode($attributes, true);

$html_editor = FormValidatorHtmlEditor :: factory(LocalSetting :: get('html_editor'), $name, $label, false, $options, $attributes);

echo $html_editor->render();
?>