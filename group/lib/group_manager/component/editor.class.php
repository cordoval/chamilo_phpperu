<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: editor.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerEditorComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = $this->get_parameter(GroupManager :: PARAM_GROUP_ID);
        if ($id)
        {
            $group = $this->retrieve_group($id);
            
            if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_EDIT, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        	{
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            $form = new GroupForm(GroupForm :: TYPE_EDIT, $group, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $id)), $this->get_user());
            
            if ($form->validate())
            {
                $success = $form->update_group();
                $group = $form->get_group();
                $this->redirect(Translation :: get($success ? 'GroupUpdated' : 'GroupNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group->get_id()));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoGroupSelected')));
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