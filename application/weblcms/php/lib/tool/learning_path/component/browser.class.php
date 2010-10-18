<?php

require_once dirname(__FILE__) . '/learning_path_browser/learning_path_cell_renderer.class.php';
require_once dirname(__FILE__) . '/learning_path_browser/learning_path_column_model.class.php';

class LearningPathToolBrowserComponent extends LearningPathTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function get_tool_actions()
    {
        $actions[] = new ToolbarItem(Translation :: get('ImportScorm'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_IMPORT_SCORM)));
        
        return $actions;
    }

    function get_content_object_publication_table_cell_renderer($tool_browser)
    {
        return new LearningPathCellRenderer($tool_browser);
    }

    function get_content_object_publication_table_column_model()
    {
        return new LearningPathColumnModel();
    }
}
?>