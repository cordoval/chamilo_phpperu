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

//$this_section = 'reporting';
$this_section = (Request :: get('application')) ? Request :: get('application') : 'reporting';
Utilities :: set_application($this_section);

$block_id = Request :: post('block');
$type = Request :: post('type');
$params = Request :: post(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS);
$post_vars = split('&', $params);
$params_final = array();
$params_final['category'] = 0;
//$params_final['url'] = '';
foreach($post_vars as $key => $value)
{
	$post_var = split('=', $value);
	$params_final[$post_var[0]] = $post_var[1];
}
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