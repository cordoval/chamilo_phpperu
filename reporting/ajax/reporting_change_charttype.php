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

$block_id = $_POST['block'];
$type = $_POST['type'];

$params_final = $_SESSION[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS];

$rdm = ReportingDataManager :: get_instance();
$block = $rdm->retrieve_reporting_block($block_id);
$block->set_displaymode($type);
//$rdm->update_reporting_block($block);


$block->set_function_parameters($params_final);
echo ReportingFormatter :: factory($block)->to_html();
?>