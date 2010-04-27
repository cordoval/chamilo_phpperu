<?php
/**
 * $Id: viewer.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_mini_month_renderer.class.php';

class PersonalCalendarManagerViewerComponent extends PersonalCalendarManager
{
    private $event;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR)), Translation :: get('PersonalCalendar')));
        
        if ($id)
        {
            $this->event = $this->retrieve_personal_calendar_publication($id);

            if (! $this->can_view())
            {
                $this->display_header($trail);
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $trail->add(new Breadcrumb($this->get_url(array(PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID => $id)), $this->event->get_publication_object()->get_title()));
            $trail->add_help('personal calender general');
            
            $this->action_bar = $this->get_action_bar();
            $output = $this->get_publication_as_html();
            
            $this->display_header($trail);
            echo '<br /><a name="top"></a>';
            echo $this->action_bar->as_html() . '<br />';
            echo '<div id="action_bar_browser">';
            echo $output;
            echo '</div>';
            
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoProfileSelected')));
        }
    }

    function can_view()
    {
        $event = $this->event;
        $user = $this->get_user();
        
        $is_target = $event->is_target($user);
        $is_publisher = ($event->get_publisher() == $user->get_id());
        $is_platform_admin = $user->is_platform_admin();
        
        if (! $is_target && ! $is_publisher && ! $is_platform_admin)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function get_publication_as_html()
    {
        $event = $this->event;
        $action_bar = $this->action_bar;
        
        $content_object = $event->get_publication_object();
        $display = ContentObjectDisplay :: factory($content_object);
        $html = array();
        
        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $view = Request :: get('view') ? Request :: get('view') : 'month';
        $this->set_parameter('time', $time);
        $this->set_parameter('view', $view);
        $this->set_parameter(Application :: PARAM_ACTION, PersonalCalendarManager :: ACTION_BROWSE_CALENDAR);
        
        $minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
        $html[] = '<div style="float: left; width: 19%; padding-right: 1%;">';
        $html[] = $minimonthcalendar->render();
        $html[] = '</div>';
        $html[] = '<div style="float: left; width: 80%;">';
        
        $html[] = $display->get_full_html();
        $html[] = '<div class="event_publication_info">';
        $html[] = htmlentities(Translation :: get('PublishedOn')) . ' ' . $this->render_publication_date();
        $html[] = htmlentities(Translation :: get('By')) . ' ' . $this->render_repo_viewer($publication);
        $html[] = htmlentities(Translation :: get('SharedWith')) . ' ' . $this->render_publication_targets();
        $html[] = '</div>';
        
        $back = $this->get_url();
        $edit_url = $this->get_publication_editing_url($event);
        $delete_url = $this->get_publication_deleting_url($event);
        $ical_url = $this->get_ical_export_url($event);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Back'), Theme :: get_common_image_path() . 'action_prev.png', $back));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ExportIcal'), Theme :: get_common_image_path() . 'export_csv.png', $ical_url));
        
        $user = $this->get_user();
        
        if ($user->is_platform_admin() || $event->get_publisher() == $user->get_id())
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url));
        }
        
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function render_repo_viewer()
    {
        $user = $this->event->get_publication_publisher();
        return $user->get_firstname() . ' ' . $user->get_lastname();
    }

    function render_publication_date()
    {
        $date_format = Translation :: get('dateTimeFormatLong');
        return DatetimeUtilities :: format_locale_date($date_format, $this->event->get_published());
    }

    function render_publication_targets()
    {
        $event = $this->event;
        
        if ($event->is_for_nobody())
        {
            return htmlentities(Translation :: get('Nobody'));
        }
        else
        {
            $users = $event->get_target_users();
            $groups = $event->get_target_groups();
            if (count($users) + count($groups) == 1)
            {
                if (count($users) == 1)
                {
                    $user = $this->get_user_info($users[0]);
                    return $user->get_firstname() . ' ' . $user->get_lastname() . $email_suffix;
                }
                else
                {
                    $gdm = GroupDatamanager :: get_instance();
                    $group = $gdm->retrieve_group($groups[0]);
                    return $group->get_name();
                }
            }
            $target_list = array();
            $target_list[] = '<select>';
            foreach ($users as $index => $user_id)
            {
                $user = $this->get_user_info($user_id);
                $target_list[] = '<option>' . $user->get_firstname() . ' ' . $user->get_lastname() . '</option>';
            }
            foreach ($groups as $index => $group_id)
            {
                $gdm = GroupDatamanager :: get_instance();
                //Todo: make this more efficient. Get all course_groups using a single query
                $group = $gdm->retrieve_group($group_id);
                $target_list[] = '<option>' . $group->get_name() . '</option>';
            }
            $target_list[] = '</select>';
            return implode("\n", $target_list);
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if (PlatformSetting :: get('allow_personal_agenda', 'user') && ! Request :: get('calendar_event'))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_CREATE_PUBLICATION))));
        }
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path() . 'tool_calendar_down.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path() . 'tool_calendar_month.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'month'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path() . 'tool_calendar_week.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'week'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path() . 'tool_calendar_day.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'day'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path() . 'tool_calendar_today.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => (Request :: get('view') ? Request :: get('view') : 'month'), 'time' => time()))));
        return $action_bar;
    }
}
?>