<?php
require_once dirname(__FILE__) . '/survey_browser/survey_cell_renderer.class.php';
require_once dirname(__FILE__) . '/survey_browser/survey_column_model.class.php';

class SurveyToolBrowserComponent extends SurveyTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $object = $publication->get_content_object();
        
        $calendar_event = ContentObject :: factory(CalendarEvent :: get_type_name());
        $calendar_event->set_title($object->get_title());
        $calendar_event->set_description($object->get_description());
        if ($publication->is_forever())
        {
            $calendar_event->set_start_date($publication->get_modified_date());
            $calendar_event->set_end_date($publication->get_modified_date());
        }
        else
        {
            $calendar_event->set_start_date($publication->get_from_date());
            $calendar_event->set_end_date($publication->get_to_date());
        }
        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_NONE);
        
        $publication->set_content_object($calendar_event);
        
        return $publication;
    }

    function get_content_object_publication_table_cell_renderer($tool_browser)
    {
        return new SurveyCellRenderer($tool_browser);
    }

    function get_content_object_publication_table_column_model()
    {
        return new SurveyColumnModel();
    }
}
?>