<?php
/**
 * $Id: creator.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerCreatorComponent extends GroupManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateGroup')));
        $trail->add_help('group general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail, false);
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
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }
}
?>