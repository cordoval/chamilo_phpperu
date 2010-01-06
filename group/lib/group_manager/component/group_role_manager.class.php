<?php
/**
 * $Id: group_role_manager.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerGroupRightsTemplateManagerComponent extends GroupManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
        $trail->add_help('group rights');
        
        $group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);
        if (! $group_id)
        {
            $this->display_header($trail);
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
            exit();
        }
        
        $group = $this->retrieve_group($group_id);
        
        $trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group_id)), Translation :: get('ModifyGroupRightsTemplates')));
        
        $form = new GroupRightsTemplateManagerForm($group, $this->get_user(), $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group_id)));
        
        if ($form->validate())
        {
            $success = $form->update_group_rights_templates();
            $this->redirect(Translation :: get($success ? 'GroupRightsTemplatesChanged' : 'GroupRightsTemplatesNotChanged'), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
        }
        else
        {
            $this->display_header($trail);
            
            echo sprintf(Translation :: get('ModifyRightsTemplatesForGroup'), $group->get_name());
            
            $form->display();
            $this->display_footer();
        }
    }
}
?>