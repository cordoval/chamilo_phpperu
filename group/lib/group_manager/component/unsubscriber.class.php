<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: unsubscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerUnsubscriberComponent extends GroupManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();

        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::UNSUBSCRIBE_RIGHT, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromGroup')));
            $trail->add_help('group unsubscribe users');

            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $ids = Request :: get(GroupManager :: PARAM_GROUP_REL_USER_ID);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $groupreluser_ids = explode('|', $id);
                $groupreluser = $this->retrieve_group_rel_user($groupreluser_ids[1], $groupreluser_ids[0]);

                if (!$groupreluser)
                    continue;

                if ($groupreluser_ids[0] == $groupreluser->get_group_id())
                {
                    if (! $groupreluser->delete())
                    {
                        $failures ++;
                    }
                    else
                    {
                    	require_once dirname(__FILE__) . '/../../../trackers/group_changes_tracker.class.php';
                    	
                        Event :: trigger('unsubscribe_user', GroupManager :: APPLICATION_NAME, array(
                                ChangesTracker :: PROPERTY_REFERENCE_ID => $groupreluser->get_group_id(),
                                GroupChangesTracker :: PROPERTY_TARGET_USER_ID => $groupreluser->get_user_id(), ChangesTracker :: PROPERTY_USER_ID => $user->get_id()));
                    }
                }
                else
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupRelUserNotDeleted';
                }
                else
                {
                    $message = 'SelectedGroupRelUsersNotDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedGroupRelUserDeleted';
                }
                else
                {
                    $message = 'SelectedGroupRelUsersDeleted';
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $groupreluser_ids[0]));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoGroupRelUserSelected')));
        }
    }
}
?>