<?php
/**
 * $Id: browser.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../personal_calendar_manager.class.php';
require_once dirname(__FILE__) . '/../personal_calendar_manager_component.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_list_renderer.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_month_renderer.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_week_renderer.class.php';
require_once dirname(__FILE__) . '/../../renderer/personal_calendar_day_renderer.class.php';
require_once dirname(__FILE__) . '/../../forms/personal_calendar_jump_form.class.php';

class PersonalCalendarManagerBrowserComponent extends PersonalCalendarManagerComponent
{  
    private $form;
    
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $view = Request :: get(PersonalCalendarManager::PARAM_VIEW) ? Request :: get(PersonalCalendarManager::PARAM_VIEW) : 'month'; 
        $this->set_parameter(PersonalCalendarManager::PARAM_VIEW, $view);
        $this->form = new PersonalCalendarJumpForm($this, $this->get_url());
        if ($this->form->validate())
        {
        	$time = $this->form->get_time();
        }
        else 
        {
        	$time = Request :: get(PersonalCalendarManager::PARAM_TIME) ? intval(Request :: get(PersonalCalendarManager::PARAM_TIME)) : time();
        }
        $this->set_parameter(PersonalCalendarManager::PARAM_TIME, $time);
    	$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PersonalCalendar')));
        $trail->add_help('personal calender general');
        
        $this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->get_action_bar_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_calendar_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_calendar_html()
    {
        $html = array();             
        $minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $this->get_parameter(PersonalCalendarManager::PARAM_TIME));
        $html[] = '<div class="mini_calendar">';
        $html[] = $minimonthcalendar->render();
		$html[] = $this->form->display();	
        $html[] = '</div>';
        $html[] = '<div class="normal_calendar">';
        $show_calendar = true;
        
        if (Request :: get('pid'))
        {
            $pid = Request :: get('pid');
            $event = $this->retrieve_calendar_event_publication($pid);
            if (Request :: get('action') && Request :: get('action') == 'delete')
            {
                $event->delete();
                $html[] = Display :: normal_message(Translation :: get('ContentObjectPublicationDeleted'), true);
            }
            else
            {
                $show_calendar = false;
                $content_object = $event->get_publication_object();
                $display = ContentObjectDisplay :: factory($content_object);
                $out .= '<h3>' . $content_object->get_title() . '</h3>';
                $out .= $display->get_full_html();
                $toolbar_data = array();
                $toolbar_data[] = array('href' => $this->get_url(), 'label' => Translation :: get('Back'), 'img' => Theme :: get_common_image_path() . 'action_prev.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
                $toolbar_data[] = array('href' => $this->get_publication_deleting_url($event), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
                $html[] = Utilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
            }
        }
        
        if ($show_calendar)
        {
            $time = $this->get_parameter(PersonalCalendarManager::PARAM_TIME);
        	switch ($this->get_parameter(PersonalCalendarManager::PARAM_VIEW))
            {
                case 'list' :
                    $renderer = new PersonalCalendarListRenderer($this, $time);
                    break;
                case 'day' :
                    $renderer = new PersonalCalendarDayRenderer($this, $time);
                    break;
                case 'week' :
                    $renderer = new PersonalCalendarWeekRenderer($this, $time);
                    break;
                default :
                    $renderer = new PersonalCalendarMonthRenderer($this, $time);
                    break;
            }
            $html[] = $renderer->render();
        }
        
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_CREATE_PUBLICATION))));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ImportIcal'), Theme :: get_common_image_path() . 'action_import.png', $this->get_ical_import_url()));
 
        if ($this->get_parameter(PersonalCalendarManager::PARAM_VIEW) == 'list')
        {
            $action_bar->set_search_url($this->get_url());
        }
        
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path() . 'tool_calendar_down.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('MonthView'), Theme :: get_image_path() . 'tool_calendar_month.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'month'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('WeekView'), Theme :: get_image_path() . 'tool_calendar_week.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'week'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('DayView'), Theme :: get_image_path() . 'tool_calendar_day.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'day'))));
        $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Today'), Theme :: get_image_path() . 'tool_calendar_today.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => $this->get_parameter(PersonalCalendarManager::PARAM_VIEW), 'time' => time()))));
        return $action_bar->as_html();
    }
}
?>