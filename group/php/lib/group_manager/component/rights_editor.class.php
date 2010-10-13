<?php
namespace group;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Request;
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: deleter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerRightsEditorComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group_ids = $this->get_parameter(GroupManager :: PARAM_GROUP_ID);

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
//        unset($array['RIGHT_EDIT']);
//        unset($array['RIGHT_DELETE']);

        return $array;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), Translation :: get('GroupManagerViewerComponent')));
    	$breadcrumbtrail->add_help('group general');
    }
    
    function get_additional_parameters()
    {
    	return array(GroupManager :: PARAM_GROUP_ID);
    }
}
?>