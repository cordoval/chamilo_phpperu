<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: creator.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerCreatorComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_CREATE, Request::get(GroupManager::PARAM_GROUP_ID)))
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $group = new Group();
        $group->set_parent(Request :: get(GroupManager :: PARAM_GROUP_ID));
        $form = new GroupForm(GroupForm :: TYPE_CREATE, $group, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), $this->get_user());

        if ($form->validate())
        {
            $success = $form->create_group();
            if ($success)
            {
                $group = $form->get_group();
                $this->redirect(Translation :: get('GroupCreated'), (false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('GroupNotCreated'), (true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('group general');
    }
    
}
?>