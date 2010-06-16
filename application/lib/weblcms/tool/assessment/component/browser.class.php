<?php
class AssessmentToolBrowserComponent extends AssessmentTool
{
    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
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

    function get_browser_type()
    {
        return ContentObjectPublicationListRenderer :: TYPE_TABLE;
    }

    function get_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }
}
?>