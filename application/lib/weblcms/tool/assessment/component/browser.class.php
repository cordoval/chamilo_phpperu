<?php
require_once dirname(__FILE__) . '/assessment_browser/assessment_cell_renderer.class.php';
require_once dirname(__FILE__) . '/assessment_browser/assessment_column_model.class.php';

class AssessmentToolBrowserComponent extends AssessmentTool
{

    function run()
    {
        $tool_component = ToolComponent :: factory(ToolComponent :: ACTION_BROWSE, $this);
        $tool_component->run();
    }

    function get_tool_actions()
    {
        $tool_actions = array();
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $tool_actions[] = new ToolbarItem(Translation :: get('ImportQti'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_IMPORT_QTI)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        }

        if ($this->is_allowed(EDIT_RIGHT))
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

    function get_content_object_publication_actions($publication)
    {
        $assessment = $publication->get_content_object();
        $track = new WeblcmsAssessmentAttemptsTracker();
        $condition_t = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ASSESSMENT_ID, $publication->get_id());
        $condition_u = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_USER_ID, $this->get_user_id());
        $condition = new AndCondition(array($condition_t, $condition_u));
        $trackers = $track->retrieve_tracker_items($condition);

        $count = count($trackers);

        foreach ($trackers as $tracker)
        {
            if ($tracker->get_status() == 'not attempted')
            {
                $this->active_tracker = $tracker;
                $count --;
                break;
            }
        }

        $extra_toolbar_items = array();

        if ($assessment->get_maximum_attempts() == 0 || $count < $assessment->get_maximum_attempts())
        {
            $extra_toolbar_items[] = new ToolbarItem(Translation :: get('TakeAssessment'), Theme :: get_common_image_path() . 'action_right.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
        }
        else
        {
            $extra_toolbar_items[] = new ToolbarItem(Translation :: get('TakeAssessment'), Theme :: get_common_image_path() . 'action_right_na.png', null, ToolbarItem :: DISPLAY_ICON);
        }

        $extra_toolbar_items[] = new ToolbarItem(Translation :: get('ViewResults'), Theme :: get_common_image_path() . 'action_view_results.png', $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
        $extra_toolbar_items[] = new ToolbarItem(Translation :: get('Export'), Theme :: get_common_image_path() . 'action_export.png', $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_QTI, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);

        return $extra_toolbar_items;
    }
}
?>