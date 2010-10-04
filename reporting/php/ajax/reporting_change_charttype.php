<?php
/**
 * $Id: reporting_change_charttype.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.ajax
 */
/**
 * This changes the reporting block displaymode
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';
/*
 * CoreApplication is required to check whether the application is
 * a core application or not.
 */
require_once dirname(__FILE__) . '/../../common/core_application.class.php';

//$this_section = 'reporting';
$this_section = (Request :: get('application')) ? Request :: get('application') : 'reporting';
Utilities :: set_application($this_section);
$block_id = Request :: post('block');
$type = Request :: post('type');
$url = Request :: post('url');
$post_vars = split('&', $url);
$params_final = array();
$params_final['category'] = 0;
foreach($post_vars as $key => $value)
{
	$post_var = split('=', $value);
	$params_final[$post_var[0]] = $post_var[1];
}
/*
 * Check whether it's a core application or not, assemble the URL and pass it to the parameters
 */
if ($params_final['application'] == in_array(CoreApplication :: get_list()))
	$params_final['url'] = 'core.php?' . $url;
else
	$params_final['url'] = 'run.php?' . $url;
$rdm = ReportingDataManager :: get_instance();
$block = $rdm->retrieve_reporting_block($block_id);
$block->set_displaymode($type);
//$rdm->update_reporting_block($block);
$block->set_function_parameters($params_final);
$displaymodes = $_SESSION['displaymodes'];
$displaymodes[$block_id] = $type;
$_SESSION['displaymodes'] = $displaymodes;
echo ReportingFormatter :: factory($block)->to_html();

?>