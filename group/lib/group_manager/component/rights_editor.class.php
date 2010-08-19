<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: deleter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerRightsEditorComponent extends GroupManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group_ids = Request :: get(GroupManager::PARAM_GROUP_ID);
        $this->set_parameter(GroupManager::PARAM_GROUP_ID, $group_ids);

        if (! is_array($group_ids))
        {
            $group_ids = array($group_ids);
        }

        $locations = array();

        foreach ($group_ids as $group_id)
        {
            $locations[] = GroupRights :: get_location_by_identifier_from_groups_subtree($group_id);
        }

        $manager = new RightsEditorManager($this, $locations);
        
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
       
    }
    function get_available_rights()
    {
        $array = GroupRights :: get_available_rights();
//        unset($array['ADD_RIGHT']);
//        unset($array['EDIT_RIGHT']);
//        unset($array['DELETE_RIGHT']);

        return $array;
    }
}
?>