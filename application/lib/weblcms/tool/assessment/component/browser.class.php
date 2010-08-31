<?php
require_once dirname(__FILE__) . '/assessment_browser/assessment_cell_renderer.class.php';
require_once dirname(__FILE__) . '/assessment_browser/assessment_column_model.class.php';

class AssessmentToolBrowserComponent extends AssessmentTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_tool_actions()
    {
        $tool_actions = array();
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $tool_actions[] = new ToolbarItem(Translation :: get('ImportQti'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_IMPORT_QTI)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        }
        
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_name = Translation :: get('ViewResultsSummary');
        }
        else
        {
            $action_name = Translation :: get('ViewResults');
        }
        $tool_actions[] = new ToolbarItem($action_name, Theme :: get_common_image_path() . 'action_view_results.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        
        return $tool_actions;
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
        return new AssessmentCellRenderer($tool_browser);
    }

    function get_content_object_publication_table_column_model()
    {
        return new AssessmentColumnModel();
    }
}
?>