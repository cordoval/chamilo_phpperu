<?php
/**
 * $Id: learning_path_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path
 */
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class LearningPathTool extends Tool implements Categorizable
{
    const ACTION_EXPORT_SCORM = 'scorm_exporter';
    const ACTION_IMPORT_SCORM = 'scorm_importer';
    const ACTION_VIEW_STATISTICS = 'statistics_viewer';
    const ACTION_VIEW_CLO = 'clo_viewer';
    const ACTION_VIEW_ASSESSMENT_CLO = 'assessment_clo_viewer';
    const ACTION_VIEW_DOCUMENT = 'document_viewer';
    
    const PARAM_OBJECT_ID = 'object_id';
    const PARAM_LEARNING_PATH = 'lp';
    const PARAM_LP_STEP = 'step';
    const PARAM_LEARNING_PATH_ID = 'lpid';
    const PARAM_ATTEMPT_ID = 'attempt_id';

    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        return $browser_types;
    }

    static function get_allowed_types()
    {
        return array(LearningPath :: get_type_name());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_content_object_publication_actions($publication)
    {
        $allowed = $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);
        
        if (! $this->is_empty_learning_path($publication))
        {
            if ($allowed)
            {
                $items[] = new ToolbarItem(Translation :: get('Statistics'), Theme :: get_common_image_path() . 'action_statistics.png', $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_STATISTICS, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
            }
        }
        else
        {
            if ($allowed)
            {
                $items[] = new ToolbarItem(Translation :: get('StatisticsNA'), Theme :: get_common_image_path() . 'action_statistics_na.png', null, ToolbarItem :: DISPLAY_ICON);
            }
        }
        
        return $items;
    }
    
    private static $checked_publications = array();

    function is_empty_learning_path($publication)
    {
        if (! array_key_exists($publication->get_id(), self :: $checked_publications))
        {
            $object = $publication->get_content_object_id();
            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object);
            $count = RepositoryDataManager :: get_instance()->count_complex_content_object_items($condition);
            
            self :: $checked_publications[$publication->get_id()] = $count == 0;
        }
        
        return self :: $checked_publications[$publication->get_id()];
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }
}
?>