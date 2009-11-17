<?php

//$Id: allowed_html_tags.php 128 2009-11-09 13:13:20Z vanpouckesven $
/**
 * @package common.html
 */
require_once ('../global.inc.php');

Translation :: set_application('general');

require_once (PATH :: get(SYS_LIB_PATH) . 'html/formvalidator/Rule/HTML.php');
// TODO: Use AdminDataManager here ...
//$language_code = Database :: get_language_isocode($language_interface);
$language_code = 'en';
header('Content-Type: text/html; charset=' . $charset);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
	xml:lang="<?php
echo $language_code;
?>"
	lang="<?php
echo $language_code;
?>">
<head>
<meta http-equiv="Content-Type"
	content="text/html; charset=<?php
echo $charset?>" />
<title>
<?php
echo Translation :: get('AllowedHTMLTags');
?>
</title>
<link rel="stylesheet" type="text/css"
	href="<?php
echo Theme :: get_common_css_path();
?>" />
</head>
<body>
<div style="margin: 10px;">
<div style="text-align: right;"><a href="javascript:window.close();"><?php
echo Translation :: get('Close');
?></a></div>
<h4>
<?php
echo Translation :: get('AllowedHTMLTags');
?>
</h4>
<?php

$html_type = $_SESSION['status'] == COURSEMANAGER ? TEACHER_HTML : STUDENT_HTML;
$fullpage = Request :: get('fullpage') == '0' ? false : true;
$tags = HTML_QuickForm_Rule_HTML :: get_allowed_tags($html_type, $fullpage);
$table_header = array();
$table_header[] = array('tag', true);
$table_header[] = array('attributes', false);
foreach ($tags as $tag => $attributes)
{
    $row = array();
    $row[] = '<kbd>' . $tag . '</kbd>';
    $row[] = '<kbd>&nbsp;' . implode(', ', array_keys($attributes)) . '</kbd>';
    $table_data[] = $row;
}

// TODO: Nicer solution for this page please ...


$sorting_options = array();
$paging_options = array();

$column = isset($sorting_options['column']) ? $sorting_options['column'] : 0;
$default_items_per_page = isset($paging_options['per_page']) ? $paging_options['per_page'] : 20;
$table = new SortableTableFromArray($table_data, $column, $default_items_per_page);
if (is_array($query_vars))
{
    $table->set_additional_parameters($query_vars);
}
foreach ($table_header as $index => $header_item)
{
    $table->set_header($index, $header_item[0], $header_item[1], $header_item[2], $header_item[3]);
}
$table->display();

?>
<div style="text-align: right;"><a href="javascript:window.close();"><?php
echo Translation :: get('Close');
?></a></div>
</div>
</body>
</html>