<?php
/**
 * $Id: block_sort.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

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

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $column_data = explode('_', $_POST['column']);
    $blocks = unserialize_jquery($_POST['order']);
    
    $hdm = HomeDataManager :: get_instance();
    
    $column = $hdm->retrieve_home_column($column_data[1]);
    
    if ($column->get_user() == $user_id)
    {
        
        $i = 1;
        foreach ($blocks as $block_id)
        {
            $block = $hdm->retrieve_home_block($block_id);
            
            $block->set_column($column->get_id());
            $block->set_sort($i);
            $block->update();
            $i ++;
        }
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