<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: mover.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerMoverComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_MOVE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $group = $this->retrieve_groups(new EqualityCondition(Group :: PROPERTY_ID, Request :: get(GroupManager :: PARAM_GROUP_ID)))->next_result();
        
        //TODO: only show groups you can actually move to (where you have create rights)
        $form = new GroupMoveForm($group, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), $this->get_user());
        
        if ($form->validate())
        {

            $success = $form->move_group();
            $parent = $form->get_new_parent();
            $this->redirect($success ? Translation :: get('GroupMoved') : Translation :: get('GroupNotMoved'), $success ? (false) : true, array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS, GroupManager :: PARAM_GROUP_ID => $parent));
        }
        else
        {
            $this->display_header();
            echo Translation :: get('Group') . ': ' . $group->get_name();
            $form->display();
            $this->display_footer();
        }
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