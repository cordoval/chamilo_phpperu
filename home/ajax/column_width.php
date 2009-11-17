<?php
/**
 * $Id: column_width.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $column_data = explode('_', $_POST['column']);
    $column_width = $_POST['width'];
    
    $hdm = HomeDataManager :: get_instance();
    
    $column = $hdm->retrieve_home_column($column_data[1]);
    
    if ($column->get_user() == $user_id)
    {
        $column->set_width($column_width);
        $column->update();
    }
}
?>