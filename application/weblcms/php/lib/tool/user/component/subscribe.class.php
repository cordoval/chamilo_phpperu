<?php
/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class UserToolSubscribeComponent extends UserTool
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course = $this->get_course();
        $users = Request :: get(UserTool :: PARAM_USERS);
        if (isset($users) && ! is_array($users))
        {
            $users = array($users);
        }
        if (isset($course))
        {
            if (isset($users) && count($users) > 0 && ($course->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin()))
            {
                $failures = 0;
                
                foreach ($users as $user_id)
                {
                    //if ($user_id != $this->get_user_id())
                    {
                        $status = Request :: get(UserTool :: PARAM_STATUS) ? Request :: get(UserTool :: PARAM_STATUS) : 5;
                        if (! $this->get_parent()->subscribe_user_to_course($course, $status, '0', $user_id))
                        {
                            $failures ++;
                        }
                    }
                }
                
                if ($failures == 0)
                {
                    $success = true;
                    
                    if (count($users) == 1)
                    {
                        $message = 'UserSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'UsersSubscribedToCourse';
                    }
                }
                elseif ($failures == count($users))
                {
                    $success = false;
                    
                    if (count($users) == 1)
                    {
                        $message = 'UserNotSubscribedToCourse';
                    }
                    else
                    {
                        $message = 'UsersNotSubscribedToCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialUsersNotSubscribedToCourse';
                }
                
                $this->redirect(Translation :: get($message), ($success ? false : true), array(Tool :: PARAM_ACTION => UserTool :: ACTION_SUBSCRIBE_USER_BROWSER));
            }
        }
    }
}
?>