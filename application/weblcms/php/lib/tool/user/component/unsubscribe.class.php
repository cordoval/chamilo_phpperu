<?php
/**
 * $Id: unsubscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class UserToolUnsubscribeComponent extends UserTool
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $course = $this->get_course();
        $users = Request :: get(UserTool :: PARAM_USERS);
        if (! is_array($users))
        {
            $users = array($users);
        }
        if (isset($course))
        {
            if (isset($users) && $course->is_course_admin($this->get_user()))
            {
                $failures = 0;
                
                foreach ($users as $user_id)
                {
                    if (!is_null($user_id) && $user_id != $this->get_user_id())
                    {
                        if (! $this->get_parent()->unsubscribe_user_from_course($course, $user_id))
                        {
                            $failures ++;
                        }
                    }
                    else
                    {
                        $failures ++;
                    }
                }
                
                if ($failures == 0)
                {
                    $success = true;
                    
                    if (count($users) == 1)
                    {
                        $message = 'UserUnsubscribedFromCourse';
                    }
                    else
                    {
                        $message = 'UsersUnsubscribedFromCourse';
                    }
                }
                elseif ($failures == count($users))
                {
                    $success = false;
                    
                    if (count($users) == 1)
                    {
                        $message = 'UserNotUnsubscribedFromCourse';
                    }
                    else
                    {
                        $message = 'UsersNotUnsubscribedFromCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialUsersNotUnsubscribedFromCourse';
                }
                
                $this->redirect(Translation :: get($message), ($success ? false : true), array(UserTool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USER_BROWSER));
            }
        }
        
    }
}
?>