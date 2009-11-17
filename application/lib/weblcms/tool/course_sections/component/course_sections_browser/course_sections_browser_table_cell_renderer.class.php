<?php
/**
 * $Id: course_sections_browser_table_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
require_once dirname(__FILE__) . '/course_sections_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../course/course_sections_table/default_course_sections_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../../course/course_section.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseSectionsBrowserTableCellRenderer extends DefaultCourseSectionsTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;
    private $count;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function CourseSectionsBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $wdm = WeblcmsDataManager :: get_instance();
        $this->count = $wdm->count_course_sections($browser->get_condition());
    }

    // Inherited
    function render_cell($column, $course_section)
    {
        if ($column === CourseSectionsBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course_section);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $course_section);
    }

    /**
     * Gets the action links to display
     * @param Course $course The course for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course_section)
    {
        $toolbar_data = array();
        
        $array = array(Translation :: get('Disabled'), Translation :: get('CourseAdministration'), Translation :: get('Links'), Translation :: get('Tools'));
        
        if (! in_array($course_section->get_name(), $array))
        {
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_UPDATE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_REMOVE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
            
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_SELECT_TOOLS_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), 'label' => Translation :: get('SelectTools'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('EditNA'), 'img' => Theme :: get_common_image_path() . 'action_edit_na.png');
            
            $toolbar_data[] = array('label' => Translation :: get('DeleteNA'), 

            'img' => Theme :: get_common_image_path() . 'action_delete_na.png');
            
            $toolbar_data[] = array('label' => Translation :: get('SelectToolsNA'), 'img' => Theme :: get_common_image_path() . 'action_move_na.png');
        }
        
        $order = $course_section->get_display_order();
        
        if ($order == 1)
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');
        }
        else
        {
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_MOVE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id(), CourseSectionsTool :: PARAM_DIRECTION => - 1)), 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
        }
        
        if ($order == $this->count)
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveDownNA'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
        }
        else
        {
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_MOVE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id(), CourseSectionsTool :: PARAM_DIRECTION => 1)), 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
        }
        
        if ($course_section->get_visible())
        {
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CHANGE_COURSE_SECTION_VISIBILITY, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), 'label' => Translation :: get('ChangeVisible'), 'img' => Theme :: get_common_image_path() . 'action_visible.png');
        }
        else
        {
            $toolbar_data[] = array('href' => $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CHANGE_COURSE_SECTION_VISIBILITY, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), 'label' => Translation :: get('ChangeVisible'), 'img' => Theme :: get_common_image_path() . 'action_invisible.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    
    }
}
?>