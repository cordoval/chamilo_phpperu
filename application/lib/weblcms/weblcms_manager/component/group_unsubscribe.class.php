<?php
/**
 * $Id: unsubscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../course/course_category_menu.class.php';
require_once dirname(__FILE__) . '/unsubscribe_browser/unsubscribe_browser_table.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsManagerGroupUnsubscribeComponent extends WeblcmsManagerComponent
{
    private $category;
    private $breadcrumbs;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->category = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);
        $course_code = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $group_ids = Request :: get(WeblcmsManager :: PARAM_GROUP);
        if (! is_array($group_ids))
        {
            $group_ids = array($group_ids);
        }
        if (isset($course_code))
        {
            $course = $this->retrieve_course($course_code);
            if (isset($group_ids) && $this->get_course()->is_course_admin($this->get_user()))
            {
                $failures = 0;

                foreach ($group_ids as $group_id)
                {
                    if (! $this->unsubscribe_group_from_course($course, $group_id))
                    {
                        $failures ++;
                    }
                }

                if ($failures == 0)
                {
                    $success = true;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupUnsubscribedFromCourse';
                    }
                    else
                    {
                        $message = 'GroupsUnsubscribedFromCourse';
                    }
                }
                elseif ($failures == count($group_ids))
                {
                    $success = false;

                    if (count($group_ids) == 1)
                    {
                        $message = 'GroupNotUnsubscribedFromCourse';
                    }
                    else
                    {
                        $message = 'GroupsNotUnsubscribedFromCourse';
                    }
                }
                else
                {
                    $success = false;
                    $message = 'PartialGroupsNotUnsubscribedFromCourse';
                }

                $this->redirect(Translation :: get($message), ($success ? false : true), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $course_code, WeblcmsManager :: PARAM_TOOL => 'user'));
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