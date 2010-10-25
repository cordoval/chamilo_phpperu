<?php
/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class UserToolGroupSubscribeComponent extends UserTool
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course = $this->get_course();
        $group_ids = Request :: get(UserTool :: PARAM_GROUPS);

        if (isset($group_ids) && ! is_array($group_ids))
        {
            $group_ids = array($group_ids);
        }

        if (isset($course))
        {
            if (isset($group_ids) && count($group_ids) > 0 && ($course->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin()))
            {
                $failures = 0;

                foreach ($group_ids as $group_id)
                {
                    if (! $this->get_parent()->subscribe_group_to_course($course, $group_id))
                    {
                        $failures ++;
                    }
                }

                if ($failures == 0)
                {
                    $success = true;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'GroupsSubscribedToCourse';
                    }
                }
                elseif ($failures == count($group_ids))
                {
                    $success = false;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupNotSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'GroupsNotSubscribedToCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialGroupsNotSubscribedToCourse';
                }

                $this->redirect(Translation :: get($message), ($success ? false : true), array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_GROUP_BROWSER));
            }
            else
            {
                $this->display_error_page(htmlentities(Translation :: get('NoGroupsSelected')));
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoCourseSelected')));
        }
    }
}
?>