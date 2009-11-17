<?php
/**
 * $Id: scale_information.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.ajax
 */
$this_section = 'laika';

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/laika/laika_data_manager.class.php';

Translation :: set_application('laika');
Theme :: set_application($this_section);

if (Authentication :: is_valid())
{
    $scale_id = Request :: post('scale');
    $scale_id = str_replace('scale_info_', '', $scale_id);
    $scale = LaikaDataManager :: get_instance()->retrieve_laika_scale($scale_id);
    $cluster = $scale->get_cluster();
    
    $json_result['success'] = '1';
    $json_result['title'] = Translation :: get('Scale') . ': ' . $scale->get_title();
    $json_result['subtitle'] = Translation :: get('Cluster') . ': ' . $cluster->get_title();
    $json_result['message'] = $scale->get_description();
}
else
{
    $json_result['success'] = '0';
    $json_result['title'] = Translation :: get('Error');
    $json_result['subtitle'] = Translation :: get('SomethingWentWrong');
    $json_result['message'] = Translation :: get('NotAuthorized');
}

echo json_encode($json_result);
?>