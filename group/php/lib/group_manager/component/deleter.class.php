<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: deleter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerDeleterComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();

        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_DELETE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DeleteGroup')));
            $trail->add_help('group general');

            $this->display_header($trail, false);
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $ids = Request :: get(GroupManager :: PARAM_GROUP_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $group = $this->retrieve_group($id);

                if (! $group->delete())
                {
                    $failures ++;
                }
                else
                {
                    Event :: trigger('delete', GroupManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $group->get_id(), ChangesTracker :: PROPERTY_USER_ID => $user->get_id()));
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupDeleted';
                }
                else
                {
                    $message = 'SelectedGroupDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupsDeleted';
                }
                else
                {
                    $message = 'SelectedGroupsDeleted';
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoGroupsSelected')));
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