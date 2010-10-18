<?php
/**
 * $Id: block_sort.php 227 2009-11-13 14:45:05Z kariboe $
 * @package application.weblcms.ajax
 */
$this_section = 'weblcms';

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';

Utilities :: set_application($this_section);

function unserialize_jquery($jquery)
{
    $block_data = explode('&', $jquery);
    $blocks = array();
    
    foreach ($block_data as $block)
    {
        $block_split = explode('=', $block);
        $blocks[] = $block_split[1];
    }
    
    return $blocks;
}

if (Authentication :: is_valid())
{
    $section_id = explode($_POST['id']);
    $blocks = unserialize_jquery($_POST['order']);
    
    $wdm = WeblcmsDataManager :: get_instance();
	$wdm->change_module_course_section($source, $target);
	
    $i = 1;
    foreach ($blocks as $block_id)
    {
	    $block = $wdm->retrieve_course_module($block_id);
        $block->set_sort($i);
        $block->update();
        $i ++;
    }
    
    $json_result['success'] = '1';
    $json_result['message'] = Translation :: get('BlockAdded');
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAuthorized');
}

// Return a JSON object
echo json_encode($json_result);
?>