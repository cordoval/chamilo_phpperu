<?php
/**
 * $Id: email_manager_component.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.email_manager
 */
class EmailManagerComponent extends SubManagerComponent
{
	function set_target_users($target_users)
    {
    	return $this->get_parent()->set_target_users($target_users);
    }
    
    function get_target_users()
    {
    	return $this->get_parent()->get_target_users();
    }
}
?>