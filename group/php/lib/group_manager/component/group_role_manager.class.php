<?php
/**
 * $Id: group_role_manager.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerGroupRightsTemplateManagerComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);
        if (! $group_id)
        {
            $this->display_header();
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
            exit();
        }
        
        $group = $this->retrieve_group($group_id);
        
        $form = new GroupRightsTemplateManagerForm($group, $this->get_user(), $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $group_id)));
        
        if ($form->validate())
        {
            $success = $form->update_group_rights_templates();
            $this->redirect(Translation :: get($success ? 'GroupRightsTemplatesChanged' : 'GroupRightsTemplatesNotChanged'), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
        }
        else
        {
            $this->display_header();
            
            echo sprintf(Translation :: get('ModifyRightsTemplatesForGroup'), $group->get_name());
            
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