<?php
/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component
 */

class GeolocationToolBrowserComponent extends GeolocationTool
{
    private $action_bar;
    private $introduction_text;

    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    function run()
    {
        $browser = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $browser->run();
    }

  /*function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $action_bar->set_search_url($this->get_url());
            if ($this->is_allowed(ADD_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(GeolocationTool :: PARAM_ACTION => GeolocationTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
        }

        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', ContentObject :: get_table_name());
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', ContentObject :: get_table_name());
            return new OrCondition($conditions);
        }

        return null;
    }*/
    function get_tool_actions()
    {
        $tool_actions = array();
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowToday'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_TODAY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowThisWeek'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_THIS_WEEK)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowThisMonth'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_THIS_MONTH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        return $tool_actions;
    }

    function get_tool_conditions()
    {
        $conditions = array();
        $filter = Request :: get(self :: PARAM_FILTER);

        switch ($filter)
        {
            case self :: FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
                break;
            case self :: FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
                break;
            case self :: FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
                break;
        }

        return $conditions;
    }

    function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $object = $publication->get_content_object();

        $calendar_event = ContentObject :: factory(CalendarEvent :: get_type_name());
        $calendar_event->set_title($object->get_title());
        $calendar_event->set_description($object->get_description());
        $calendar_event->set_start_date($publication->get_modified_date());
        $calendar_event->set_end_date($publication->get_modified_date());
        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_NONE);

        $publication->set_content_object($calendar_event);

        return $publication;
    }

    function get_browser_type()
    {
        return ContentObjectPublicationListRenderer :: TYPE_LIST;
    }

    function get_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }
}
?>