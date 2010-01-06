<?php
/**
 * $Id: mover.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerMoverComponent extends GroupManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add_help('group general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        $group = $this->retrieve_groups(new EqualityCondition(Group :: PROPERTY_ID, Request :: get(GroupManager :: PARAM_GROUP_ID)))->next_result();
        
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), $group->get_name()));
        
        $form = new GroupMoveForm($group, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->move_group();
            $parent = $form->get_new_parent();
            $this->redirect($success ? Translation :: get('GroupMoved') : Translation :: get('GroupNotMoved'), $success ? (false) : true, array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS, GroupManager :: PARAM_GROUP_ID => $parent));
        }
        else
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Move')));
            $this->display_header($trail);
            echo Translation :: get('Group') . ': ' . $group->get_name();
            $form->display();
            $this->display_footer();
        }
    }
}
?>