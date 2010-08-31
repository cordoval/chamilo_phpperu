<?php
require_once dirname(__FILE__) ."/../../personal_messenger_rights.class.php";
/**
 * $Id: deleter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class PersonalMessengerManagerRightsEditorComponent extends PersonalMessengerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $locations = array();
        $locations[] = PersonalMessengerRights :: get_personal_messenger_subtree_root();

        if($this->get_user()->is_platform_admin())
        {
            $manager = new RightsEditorManager($this, $locations);
        
            $manager->exclude_users(array($this->get_user_id()));
            $manager->run();
        }
        else
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
    }
    
    function get_available_rights()
    {
        $array = PersonalMessengerRights :: get_available_rights();
        return $array;
    }
}
?>