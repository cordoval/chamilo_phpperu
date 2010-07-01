<?php
/**
 * $Id: shortcut_tool_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool_list_renderer
 */
require_once (dirname(__FILE__) . '/../tool_list_renderer.class.php');
require_once ('HTML/Table.php');
/**
 * Tool list renderer to display a navigation menu.
 */
class ShortcutToolListRenderer extends ToolListRenderer
{
    // Inherited
    function display()
    {
        $parent = $this->get_parent();
        $this->show_tools($this->get_visible_tools());
    }

    /**
     * Show the tools of a given section
     * @param array $tools
     */
    private function show_tools($tools)
    {
        $parent = $this->get_parent();
        $course = $parent->get_course();
        
        $tools_shown = array();
        
        foreach ($tools as $index => $tool)
        {
            $sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition(CourseSection :: PROPERTY_ID, $tool->section));
            $section = $sections->next_result();
            
            $new = '';
            if ($parent->tool_has_new_publications($tool->name))
            {
                $new = '_new';
            }
            
            $tool_image = 'tool_mini_' . $tool->name . $new . '.png';
            $title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name) . 'Title'));
            $html[] = '<a href="' . $parent->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_TOOL => $tool->name), array(), true) . '" title="' . $title . '">';
            $html[] = '<img src="' . Theme :: get_image_path() . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/> ';
            $html[] = '</a>';
        }
        
        echo implode("\n", $html);
    }
}
?>