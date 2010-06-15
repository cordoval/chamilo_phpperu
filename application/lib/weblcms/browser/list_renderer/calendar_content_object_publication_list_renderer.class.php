<?php
/**
 * $Id: week_calendar_content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../content_object_publication_list_renderer.class.php';
/**
 * Interval between sections in the week view of the calendar.
 */
/**
 * Renderer to display events in a week calendar
 */
class CalendarContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{
    const CALENDAR_MONTH_VIEW = 'month';
    const CALENDAR_WEEK_VIEW = 'week';
    const CALENDAR_DAY_VIEW = 'day';
    
    const PARAM_CALENDAR_VIEW = 'view';
    
    /**
     * The current time displayed in the calendar
     */
    private $display_time;

    /**
     * Sets the current display time.
     * @param int $time The current display time.
     */
    function set_display_time($time)
    {
        $this->display_time = $time;
    }

    function get_display_time()
    {
        return $this->display_time;
    }

    function get_view()
    {
        return Request :: get(self :: PARAM_CALENDAR_VIEW);
    }

    function get_calendar_events($from_time, $to_time, $limit = 0)
    {
        $publications = $this->get_publications();
        
        $events = array();
        foreach ($publications as $index => $publication)
        {
            if (method_exists($this->get_browser()->get_parent(), 'convert_content_object_publication_to_calendar_event'))
            {
                $publication = $this->get_browser()->get_parent()->convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time);
            }
            
            $object = $publication->get_content_object();
            
            if ($object->repeats())
            {
                $repeats = $object->get_repeats($from_time, $to_time);
                
                foreach ($repeats as $repeat)
                {
                    $the_publication = clone $publication;
                    $the_publication->set_content_object($repeat);
                    
                    $events[$repeat->get_start_date()] = $the_publication;
                }
            }
            elseif ($from_time <= $object->get_start_date() && $object->get_start_date() <= $to_time || $from_time <= $object->get_end_date() && $object->get_end_date() <= $to_time || $object->get_start_date() <= $from_time && $to_time <= $object->get_end_date())
            {
                $events[$object->get_start_date()] = $publication;
            }
        }
        
        return $events;
    }

    function as_html()
    {
        $time = Request :: get('time') ? intval(Request :: get('time')) : time();
        $this->set_display_time($time);
        
        $mini_month_calendar = ContentObjectPublicationListRenderer :: factory(ContentObjectPublicationListRenderer :: TYPE_MINI_MONTH, $this->get_browser());
        $mini_month_calendar->set_display_time($this->get_display_time());
        $html[] = '<div class="mini_calendar">';
        $html[] = $mini_month_calendar->as_html();
        $html[] = '<br />';
        $html[] = $this->list_views();
        $html[] = $this->render_upcoming_events();
        $html[] = '</div>';
        //style="margin-left: 0px; float: right; width: 70%;"
        $html[] = '<div class="normal_calendar">';
        
        $view = $this->get_view();
        
        switch ($view)
        {
            case self :: CALENDAR_DAY_VIEW :
                $calendar = ContentObjectPublicationListRenderer :: factory(ContentObjectPublicationListRenderer :: TYPE_DAY, $this->get_browser());
                break;
            case self :: CALENDAR_WEEK_VIEW :
                $calendar = ContentObjectPublicationListRenderer :: factory(ContentObjectPublicationListRenderer :: TYPE_WEEK, $this->get_browser());
                break;
            case self :: CALENDAR_MONTH_VIEW :
                $calendar = ContentObjectPublicationListRenderer :: factory(ContentObjectPublicationListRenderer :: TYPE_MONTH, $this->get_browser());
                break;
            default :
                $calendar = ContentObjectPublicationListRenderer :: factory(ContentObjectPublicationListRenderer :: TYPE_MONTH, $this->get_browser());
                break;
        }
        
        $calendar->set_display_time($time);
        $html[] = $calendar->as_html();
        
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function get_filter_targets()
    {
        $course = $this->get_course_id();
        
        $targets = array();
        
        $user_conditions = array();
        $user_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course);
        $user_conditions[] = new NotCondition(new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id()));
        $user_condition = new AndCondition($user_conditions);
        
        $user_relations = WeblcmsDataManager :: get_instance()->retrieve_course_user_relations($user_condition);
        if ($user_relations->size() > 0)
        {
            $targets[] = Translation :: get('Users');
            $targets[] = '----------';
            
            while ($user_relation = $user_relations->next_result())
            {
                $user = $user_relation->get_user_object();
                
                $targets['user|' . $user->get_id()] = $user->get_fullname() . ' (' . $user->get_username() . ')';
            }
        }
        
        $groups = WeblcmsDataManager :: get_instance()->retrieve_course_groups(new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $course));
        if ($groups->size() > 0)
        {
            if ($user_relations->size() > 0)
            {
                $targets[] = '';
            }
            
            $targets[] = Translation :: get('Groups');
            $targets[] = '----------';
            
            while ($group = $groups->next_result())
            {
                $targets['group|' . $group->get_id()] = $group->get_name();
            }
        }
        
        return $targets;
    }

    function list_views()
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_VERTICAL);
        $toolbar->add_item(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path() . 'tool_calendar_month.png', $this->get_url(array(self :: PARAM_CALENDAR_VIEW => self :: CALENDAR_MONTH_VIEW, 'time' => $this->get_display_time())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path() . 'tool_calendar_week.png', $this->get_url(array(self :: PARAM_CALENDAR_VIEW => self :: CALENDAR_WEEK_VIEW, 'time' => $this->get_display_time())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path() . 'tool_calendar_day.png', $this->get_url(array(self :: PARAM_CALENDAR_VIEW => self :: CALENDAR_DAY_VIEW, 'time' => $this->get_display_time())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path() . 'tool_calendar_today.png', $this->get_url(array(self :: PARAM_CALENDAR_VIEW => $this->get_view(), 'time' => time())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        $html = array();
        $html[] = '<div class="content_object" style="padding: 10px;">';
        $html[] = '<div class="description">';
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $form = new FormValidator('user_filter', 'post', $this->get_url());
            $renderer = $form->defaultRenderer();
            $renderer->setElementTemplate('{element}');
            $form->addElement('select', 'filter', Translation :: get('FilterTarget'), $this->get_filter_targets());
            $form->addElement('submit', 'submit', Translation :: get('Ok'));
            
            $html[] = $form->toHtml();
            $html[] = '<br />';
        }
        
        $html[] = $toolbar->as_html();
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function render_upcoming_events()
    {
        $html = array();
        
        $amount_to_show = 5;
        $publications = $this->get_calendar_events(time(), strtotime('+1 Year', time()), $amount_to_show);
        ksort($publications);
        $count = count($publications);
        $total = $count < $amount_to_show ? $count : $amount_to_show;
        
        if (count($publications) > 0)
        {
            $html[] = '<div class="content_object" style="padding: 10px;">';
            $html[] = '<div class="title">' . Translation :: get('UpcomingEvents') . '</div>';
            $html[] = '<div class="description">';
            
            $i = 0;
            foreach ($publications as $publication)
            {
                $html[] = $this->render_small_publication($publication);
                $i ++;
                
                if ($i >= $amount_to_show)
                {
                    break;
                }
            }
            $html[] = '</div>';
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }

    function render_small_publication($publication)
    {
        $feedback_url = $this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_VIEW), array(), true);
        
        return '<a href="' . $feedback_url . '">' . date('d/m/y H:i:s -', $publication->get_content_object()->get_start_date()) . ' ' . $publication->get_content_object()->get_title() . '</a><br />';
    }
}
?>