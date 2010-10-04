<?php
/**
 * $Id: tab_sort.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';

function unserialize_jquery($jquery)
{
    $element_data = explode('&', $jquery);
    $elements = array();
    
    foreach ($element_data as $element)
    {
        $element_split = explode('=', $element);
        $elements[] = $element_split[1];
    }
    
    return $elements;
}

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $tabs = unserialize_jquery($_POST['order']);
    
    $hdm = HomeDataManager :: get_instance();
    
    $i = 1;
    foreach ($tabs as $tab_id)
    {
        $tab = $hdm->retrieve_home_tab($tab_id);
        $tab->set_sort($i);
        $tab->update();
        $i ++;
    }
}
?>