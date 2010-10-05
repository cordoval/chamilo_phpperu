<?php
/**
 * $Id: group_users_subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class UserToolGroupUsersSubscribeComponent extends UserTool
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course = $this->get_course();
        $groups = Request :: get(UserTool :: PARAM_GROUPS);

        if (! is_array($groups))
        {
            $groups = array($groups);
        }
        if (isset($course))
        {
            if (isset($groups) && $course->is_course_admin($this->get_user()))
            {
                foreach ($groups as $group_id)
                {
                    $this->subscribe_group($group_id, $course);
                }

                $success = true;

                if (count($groups) == 1)
                {
                    $message = 'GroupsSubscribedToCourse';
                }
                else
                {
                    $message = 'GroupsSubscribedToCourse';
                }

                $this->redirect(Translation :: get($message), ($success ? false : true), array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUP_BROWSER));
            }
        }

    }

    function subscribe_group($group_id, $course)
    {
        $gdm = GroupDataManager :: get_instance();
        $group_users = $gdm->retrieve_group_rel_users(new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group_id));

        while ($user = $group_users->next_result())
        {
            $user_id = $user->get_user_id();
            if ($user_id != $this->get_user_id())
            {
                $status = Request :: get(WeblcmsManager :: PARAM_STATUS) ? Request :: get(WeblcmsManager :: PARAM_STATUS) : 5;
                $this->get_parent()->subscribe_user_to_course($course, $status, '0', $user_id);
            }
        }

        $groups = $gdm->retrieve_groups(new EqualityCondition(Group :: PROPERTY_PARENT, $group_id));

        while ($group = $groups->next_result())
        {
            $this->subscribe_group($group->get_id(), $course);
        }
    }
}
?>