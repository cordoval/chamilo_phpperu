<?php
/**
 * $Id: calendar_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.calendar.component.calendar_viewer
 */
require_once dirname(__FILE__) . '/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__) . '/calendar_list_renderer.class.php';
require_once dirname(__FILE__) . '/calendar_details_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/mini_month_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/month_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/week_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/day_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class CalendarBrowser extends ContentObjectPublicationBrowser
{
    const CALENDAR_MONTH_VIEW = 'month';
    const CALENDAR_WEEK_VIEW = 'week';
    const CALENDAR_DAY_VIEW = 'day';
    const CALENDAR_LIST_VIEW = 'list';
    private $publications;
    private $time;

    function CalendarBrowser($parent)
    {
        parent :: __construct($parent, 'calendar');
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $this->set_publication_id(Request :: get(Tool :: PARAM_PUBLICATION_ID));
            //$renderer = new ContentObjectPublicationDetailsRenderer($this);
            $renderer = new CalendarDetailsRenderer($this);
        }
        else
        {
            $time = Request :: get('time') ? intval(Request :: get('time')) : time();
            $this->time = $time;
            //$this->set_parameter('time',$time);
            

            switch (Request :: get('view'))
            {
                case CalendarBrowser :: CALENDAR_DAY_VIEW :
                    {
                        $renderer = new DayCalendarContentObjectPublicationListRenderer($this);
                        $renderer->set_display_time($time);
                        break;
                    }
                case CalendarBrowser :: CALENDAR_WEEK_VIEW :
                    {
                        $renderer = new WeekCalendarContentObjectPublicationListRenderer($this);
                        $renderer->set_display_time($time);
                        break;
                    }
                case CalendarBrowser :: CALENDAR_MONTH_VIEW :
                    {
                        $renderer = new MonthCalendarContentObjectPublicationListRenderer($this);
                        $renderer->set_display_time($time);
                        break;
                    }
                case CalendarBrowser :: CALENDAR_LIST_VIEW :
                    {
                        $renderer = new CalendarListRenderer($this);
                        //$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), Tool :: ACTION_HIDE => Translation :: get('Hide'), Tool :: ACTION_SHOW => Translation :: get('Show'));
                        
                        $actions[] = new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('DeleteSelected'));
        				$actions[] = new ObjectTableFormAction(Tool :: ACTION_HIDE, Translation :: get('Hide'), false);
        				$actions[] = new ObjectTableFormAction(Tool :: ACTION_SHOW, Translation :: get('Show'), false);
                        
                        $renderer->set_actions($actions);
                        break;
                    }
                default :
                    {
                        $renderer = new MonthCalendarContentObjectPublicationListRenderer($this);
                        $renderer->set_display_time($time);
                        break;
                    }
            }
        }
        
        $this->set_publication_list_renderer($renderer);
    }

    function get_publications($from, $count, $column, $direction)
    {
        if (isset($this->publications))
        {
            return $this->publications;
        }
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $user_id = array();
            $course_group_ids = array();
            
            $filter = Request :: post('filter');
            
            if($filter)
            {
            	if(strpos($filter, 'user') !== false)
            	{
            		$user_id = substr($filter, 5);
            	}
            	
            	if(strpos($filter, 'group') !== false)
            	{
            		$course_groups = array(substr($filter, 6));
            	}
            }
        }
        else
        {
        	$user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();
                
            $course_group_ids = array();
                
            foreach($course_groups as $course_group)
            {
              	$course_group_ids[] = $course_group->get_id();
            }
        }
        
        $datamanager = WeblcmsDataManager :: get_instance();
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'calendar');
        
        $access = array();
        $access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
        $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
        if (! empty($user_id) || ! empty($course_group_ids))
        {
            $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
        }
        $conditions[] = new OrCondition($access);
        
        $subselect_conditions = array();
        $subselect_conditions[] = new EqualityCondition('type', 'calendar_event');
        if ($this->get_parent()->get_condition())
        {
            $subselect_conditions[] = $this->get_parent()->get_condition();
        }
        $subselect_condition = new AndCondition($subselect_conditions);
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        if($column)
        {
        	$order = new ObjectTableOrder($column, $direction);
        }
        $this->publications = $datamanager->retrieve_content_object_publications_new($condition, $order)->as_array();
        return $this->publications;
    }

    function get_publication_count()
    {
        return count($this->get_publications());
    }

    /**
     * Get calendar events in a certain time range
     * @param int $from_time
     * @param int $to_time
     * @return array A set of publications of calendar_events
     */
    function get_calendar_events($from_time, $to_time)
    {
        $publications = $this->get_publications();
        
        $events = array();
        foreach ($publications as $index => $publication)
        {
            $object = $publication->get_content_object();
            
            if ($object->repeats())
            {
                $repeats = $object->get_repeats($from_time, $to_time);
                
                foreach ($repeats as $repeat)
                {
                    $the_publication = clone $publication;
                    $the_publication->set_content_object($repeat);
                    
                    $events[] = $the_publication;
                }
            }
            elseif ($from_time <= $object->get_start_date() && $object->get_start_date() <= $to_time || $from_time <= $object->get_end_date() && $object->get_end_date() <= $to_time || $object->get_start_date() <= $from_time && $to_time <= $object->get_end_date())
            {
                $events[] = $publication;
            }
        }
        
        return $events;
    }

    public function as_html()
    {
        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $minimonthcalendar = new MiniMonthCalendarContentObjectPublicationListRenderer($this);
            $minimonthcalendar->set_display_time($this->time);
            $html[] = '<div class="mini_calendar">';
            $html[] = $minimonthcalendar->as_html(); 
            $html[] = '<br />';
            $html[] = $this->render_upcomming_events();
            $html[] = '</div>';
            //style="margin-left: 0px; float: right; width: 70%;"
            $html[] = '<div class="normal_calendar">';
            
            if($this->get_parent()->is_allowed(EDIT_RIGHT) && get_class(parent :: get_publication_list_renderer()) == 'CalendarListRenderer')
            {
            	$html[] = '<div style="float: right;">';
            	
            	$form = new FormValidator('user_filter', 'post', $this->get_parent()->get_url());
            	$renderer = $form->defaultRenderer();
            	$renderer->setElementTemplate('{element}');
            	$form->addElement('select', 'filter', Translation :: get('FilterTarget'), $this->get_filter_targets());
            	$form->addElement('submit', 'submit', Translation :: get('Ok'));
            	
            	$html[] = $form->toHtml();
            	$html[] = '<div class="clear"></div></div>';
            	$html[] = '<br />';
            }
            
            $html[] = parent :: as_html();
            $html[] = '</div>';
        }
        else
        {
            $html[] = parent :: as_html();
        }
        return implode("\n", $html);
    }
    
    function get_filter_targets()
    {
    	$course = $this->get_parent()->get_course_id();
    	
    	$targets = array();
        $targets[] = Translation :: get('Users');
        $targets[] = '----------';
        
        $users =  WeblcmsDataManager :: get_instance()->retrieve_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course));
    	while($user = $users->next_result())
        {
        	if($user->get_user() == $this->get_parent()->get_user_id())
        		continue;		
        
        	$targets['user|' . $user->get_user()] = UserDataManager :: get_instance()->retrieve_user($user->get_user())->get_username();
        }
        
        $targets[] = '';
        $targets[] = Translation :: get('Groups');
        $targets[] = '----------';

        $groups = WeblcmsDataManager :: get_instance()->retrieve_course_groups(new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, $course));
        while($group = $groups->next_result())
        {
        	$targets['group|' . $group->get_id()] = $group->get_name();	
        }

        return $targets;
    }
    
    function get_upcomming_events($amount)
    {
    	$from_time = time();
    	$publications = $this->get_publications();
        
        $events = array();
        foreach ($publications as $index => $publication)
        {
            $object = $publication->get_content_object();
            
            if ($object->repeats())
            {
                $repeats = $object->get_repeats($from_time, 9999999999);
                
                foreach ($repeats as $repeat)
                {
                    $the_publication = clone $publication;
                    $the_publication->set_content_object($repeat);
                    
                    $events[] = $the_publication;
                }
            }
            elseif ($from_time <= $object->get_start_date())
            {
                $events[] = $publication;
            }
            
            if(count($events) > $amount)
            	return $events;
        }
        
        return $events;
    }
    
    function render_upcomming_events()
    {
    	$html = array();
    	$html[] = '<b>' . Translation :: get('UpcommingEvents') . '</b><br /><br />';
    	
    	$amount_to_show = 5;
    	$publications = $this->get_upcomming_events($amount_to_show);
    	$count = count($publications);						
    	$total = $count < $amount_to_show ? $count : $amount_to_show;
    
    	for($i = 0; $i < $total; $i++)
    	{
    		$html[] = $this->render_small_publication($publications[$i]);
    	}
    	
    	return implode("\n", $html);	
    }
    
    function render_small_publication($publication)
    {
    	$feedback_url = $this->get_parent()->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'), array(), true);
    	
    	return '<a href="' . $feedback_url . '">' . date('d/m/y H:i:s -', $publication->get_content_object()->get_start_date()) . ' ' . 
    	 	   $publication->get_content_object()->get_title() . '</a><br />';
    }
    
}
?>