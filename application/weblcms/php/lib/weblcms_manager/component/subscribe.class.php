<?php
namespace application\weblcms;

use common\libraries\PatternMatchCondition;
use reporting\ReportingManager;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Application;
use common\libraries\Theme;
use common\libraries\OrCondition;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: subscribe.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component
 */

require_once dirname(__FILE__) . '/../../course/course_category_menu.class.php';
require_once dirname(__FILE__) . '/course_browser/course_browser_table.class.php';

/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsManagerSubscribeComponent extends WeblcmsManager
{

    private $category;
    private $action_bar;
    private $breadcrumbs;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->category = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);
        $course_code = Request :: get(WeblcmsManager :: PARAM_COURSE);
        $users = Request :: get(WeblcmsManager :: PARAM_USERS);
        if (isset($users) && ! is_array($users))
        {
            $users = array($users);
        }
        if (isset($course_code))
        {
            $course = $this->retrieve_course($course_code);
            if (isset($users) && count($users) > 0 && ($this->get_course()->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin()))
            {
                $failures = 0;

                foreach ($users as $user_id)
                {
                    //if ($user_id != $this->get_user_id())
                    {
                        $status = Request :: get(WeblcmsManager :: PARAM_STATUS) ? Request :: get(WeblcmsManager :: PARAM_STATUS) : 5;
                        if (! $this->subscribe_user_to_course($course, $status, '0', $user_id))
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

                $this->redirect(Translation :: get($message), ($success ? false : true), array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $course_code, WeblcmsManager :: PARAM_TOOL => 'user'));
            }
            else
            {
                $success = $this->subscribe_user_to_course($course, '5', '0', $this->get_user_id());
                $params = null;
                $filters = null;
                if ($course->get_access())
                    $params = array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_COURSE => $course_code);
                else
                {
                    $params = array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_WEBLCMS_HOME);
                    $filters = array(WeblcmsManager :: PARAM_COURSE);
                }
                $this->redirect(Translation :: get($success ? 'UserSubscribedToCourse' : 'UserNotSubscribedToCourse'), ($success ? false : true), $params, $filters);
            }
        }

        $trail = BreadcrumbTrail :: get_instance();

        $this->action_bar = $this->get_action_bar();

        $menu = $this->get_menu_html();

        if (! empty($this->category))
            $trail->add(new Breadcrumb($this->breadcrumbs[0]['url'], $this->breadcrumbs[0]['title']));

        $output = $this->get_course_html();

        $this->display_header();
        echo '<div class="clear"></div>';
        echo '<br />' . $this->action_bar->as_html() . '<br />';
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array('category' => Request :: get('category'))));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null ,Utilities:: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_course_html()
    {
        $table = new CourseBrowserTable($this, null, $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_menu_html()
    {
        $extra_items = array();
        if ($this->get_search_validate())
        {
            // $search_url = $this->get_url();
            $search_url = '#';
            $search = array();
            $search['title'] = Translation :: get('SearchResults', null ,Utilities:: COMMON_LIBRARIES);
            $search['url'] = $search_url;
            $search['class'] = 'search_results';
            $extra_items[] = $search;
        }
        else
        {
            $search_url = null;
        }

        $temp_replacement = '__CATEGORY_ID__';
        $url_format = $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_MANAGER_SUBSCRIBE, WeblcmsManager :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
        $url_format = str_replace($temp_replacement, '%s', $url_format);
        $category_menu = new CourseCategoryMenu($this->category, $url_format);
        $this->breadcrumbs = $category_menu->get_breadcrumbs();

        if (isset($search_url))
        {
            $category_menu->forceCurrentUrl($search_url, true);
        }

        $html = array();
        $html[] = '<div style="float: left; width: 20%;">';
        $html[] = $category_menu->render_as_tree();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();

        if (isset($query) && $query != '')
        {
            $conditions = array();
            $conditions[] = new PatternMatchCondition(Course :: PROPERTY_VISUAL, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(Course :: PROPERTY_NAME, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(CourseSettings :: PROPERTY_LANGUAGE, '*' . $query . '*', CourseSettings :: get_table_name());

            $search_conditions = new OrCondition($conditions);
        }

        $condition = null;
        if (isset($this->category))
        {
            $condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $this->category);

            if (count($search_conditions))
            {
                $condition = new AndCondition($condition, $search_conditions);
            }
        }
        else
        {
            if (count($search_conditions))
            {
                $condition = $search_conditions;
            }
        }

        $visibility_conditions = array();

        $visibility_and_conditions[] = new EqualityCondition(CourseSettings :: PROPERTY_VISIBILITY, '1', CourseSettings :: get_table_name());

        $visibility_or_conditions[] = new EqualityCondition(CourseType :: PROPERTY_ACTIVE, '1', CourseType :: get_table_name());

        //typeless courses are always active. Condition below needed because typeless is not defined in database (no typeless row in CourseType table, so no active property)
        $visibility_or_conditions[] = new EqualityCondition(Course :: PROPERTY_COURSE_TYPE_ID, '0', Course :: get_table_name());

        $visibility_and_conditions[] = new OrCondition($visibility_or_conditions);

        $visibility_condition = new AndCondition($visibility_and_conditions);

        if (is_null($condition))
            $condition = $visibility_condition;
        else
            $condition = new AndCondition($condition, $visibility_condition);

        return $condition;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('weblcms_subscribe');
    }

    function get_additional_parameters()
    {
        return array(ReportingManager :: PARAM_TEMPLATE_ID);
    }

}

?>