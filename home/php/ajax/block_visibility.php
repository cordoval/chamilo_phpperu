<?php
/**
 * $Id: block_visibility.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $block_data = explode('_', $_POST['block']);
    
    $hdm = HomeDataManager :: get_instance();
    
    $block = $hdm->retrieve_home_block($block_data[1]);
    
    if ($block->get_user() == $user_id)
    {
        if ($block->is_visible())
        {
            $block->set_invisible();
        }
        else
        {
            $block->set_visible();
        }
        $block->update();
    }
}
?>