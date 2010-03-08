<?php
require_once dirname(__FILE__) . '/../../global.inc.php';

// Getting some properties from the Ajax post
$name = Request :: post('name');
$label = Request :: post('label');
$options = Request :: post('options');
$attributes = Request :: post('attributes');

$html_editor = FormValidatorHtmlEditor :: factory(LocalSetting :: get('html_editor'), $name, $label, false, $options, $attributes);

$output = array();
$output['html'] = $html_editor->render();

echo json_encode($output);
?>