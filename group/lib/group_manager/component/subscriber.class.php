<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: subscriber.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */
   
class GroupManagerSubscriberComponent extends GroupManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = $this->get_user();
        $group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);
        if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_SUBSCRIBE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
            $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SubscribeToGroup')));
            $trail->add_help('group general');

            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        $users = Request :: get(GroupManager :: PARAM_USER_ID);

        $failures = 0;

        if (! empty($users))
        {
            if (! is_array($users))
            {
                $users = array($users);
            }

            foreach ($users as $user)
            {
                $existing_groupreluser = $this->retrieve_group_rel_user($user, $group_id);

                if (! is_null($existing_groupreluser))
                {
                    $groupreluser = new GroupRelUser();
                    $groupreluser->set_group_id($group_id);
                    $groupreluser->set_user_id($user);

                    if (! $groupreluser->create())
                    {
                        $failures ++;
                    }
                    else
                    {
         				require_once dirname(__FILE__) . '/../../../trackers/group_changes_tracker.class.php';
                    	Event :: trigger('subscribe_user', GroupManager :: APPLICATION_NAME, array(
                                ChangesTracker :: PROPERTY_REFERENCE_ID => $groupreluser->get_group_id(), GroupChangesTracker :: PROPERTY_TARGET_USER_ID => $groupreluser->get_user_id(),
                                ChangesTracker :: PROPERTY_USER_ID => $this->get_user()->get_id()));
                    }
                }
                else
                {
                    $contains_dupes = true;
                }
            }

            if ($failures)
            {
                if (count($users) == 1)
                {
                    $message = 'SelectedUserNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedUsersNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
            }
            else
            {
                if (count($users) == 1)
                {
                    $message = 'SelectedUserAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
                else
                {
                    $message = 'SelectedUsersAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group_id));
            exit();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoGroupRelUserSelected')));
        }
    }
}
?>