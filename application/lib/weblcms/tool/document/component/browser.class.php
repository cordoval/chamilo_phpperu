<?php
require_once dirname(__FILE__) . '/document_browser/document_cell_renderer.class.php';

class DocumentToolBrowserComponent extends DocumentTool
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }

    function get_tool_actions()
    {
        $tool_actions = array();
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowToday'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_TODAY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowThisWeek'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_THIS_WEEK)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowThisMonth'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_THIS_MONTH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path() . 'action_save.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_ZIP_AND_DOWNLOAD)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('Slideshow'), Theme :: get_common_image_path() . 'action_slideshow.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_SLIDESHOW)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
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

    function get_content_object_publication_table_cell_renderer($tool_browser)
    {
        return new DocumentCellRenderer($tool_browser);
    }

    function get_browser_type()
    {
        return ContentObjectPublicationListRenderer :: TYPE_TABLE;
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