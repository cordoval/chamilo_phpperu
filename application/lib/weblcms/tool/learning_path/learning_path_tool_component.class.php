<?php
/**
 * $Id: learning_path_tool_component.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path
 */
require_once dirname(__FILE__) . '/learning_path_tool.class.php';

class LearningPathToolComponent extends ToolComponent
{

    static function factory($component_name, $learning_path_tool)
    {
        return parent :: factory('LearningPath', $component_name, $learning_path_tool);
    }

}
?>