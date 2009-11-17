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
    /**
     *
     */
    private $is_course_admin;

    /**
     * Constructor
     * @param  WebLcms $parent The parent application
     */
    function MenuToolListRenderer($parent)
    {
        parent :: ToolListRenderer($parent);
        $this->is_course_admin = $this->get_parent()->is_allowed(EDIT_RIGHT);
    }

    /**
     * Sets the type of this navigation menu renderer
     * @param int $type
     */
    function set_type($type)
    {
        $this->type = $type;
    }

    // Inherited
    function display()
    {
        $parent = $this->get_parent();
        $tools = $parent->get_registered_tools();
        $this->show_tools($tools);
    }

    /**
     * Show the tools of a given section
     * @param array $tools
     */
    private function show_tools($tools)
    {
        $parent = $this->get_parent();
        $course = $parent->get_course();
        
        foreach ($tools as $index => $tool)
        {
            $sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $tool->section));
            $section = $sections->next_result();
            
            if (! PlatformSetting :: get($tool->name . '_active', 'weblcms') && $section->get_type() != CourseSection :: TYPE_ADMIN)
                continue;
            
            if ((($tool->visible && $tool->section != 'course_admin') || $this->is_course_admin) && $tool->visible)
            {
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
        }
        echo implode("\n", $html);
    }
}
?>