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
    	$toolbar = new Toolbar();
        
        $array = array(Translation :: get('Disabled'), Translation :: get('CourseAdministration'), Translation :: get('Links'), Translation :: get('Tools'));
        
        if (! in_array($course_section->get_name(), $array))
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_UPDATE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), ToolbarItem::DISPLAY_ICON ));
        	            
        	$toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_REMOVE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), ToolbarItem::DISPLAY_ICON, true ));
        	         
        	$toolbar->add_item(new ToolbarItem(Translation :: get('SelectTools'), Theme :: get_common_image_path() . 'action_move.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_SELECT_TOOLS_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), ToolbarItem::DISPLAY_ICON ));
            
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('EditNA'), Theme :: get_common_image_path() . 'action_edit_na.png', null, ToolbarItem::DISPLAY_ICON ));
            $toolbar->add_item(new ToolbarItem(Translation :: get('DeleteNA'), Theme :: get_common_image_path() . 'action_delete_na.png', null, ToolbarItem::DISPLAY_ICON ));
            $toolbar->add_item(new ToolbarItem(Translation :: get('SelectToolsNA'), Theme :: get_common_image_path() . 'action_move_na.png', null, ToolbarItem::DISPLAY_ICON ));
        }
        
        $order = $course_section->get_display_order();
        
        if ($order == 1)
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem::DISPLAY_ICON ));
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_MOVE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id(), CourseSectionsTool :: PARAM_DIRECTION => - 1)), ToolbarItem::DISPLAY_ICON ));
        }
        
        if ($order == $this->count)
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem::DISPLAY_ICON ));
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_MOVE_COURSE_SECTION, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id(), CourseSectionsTool :: PARAM_DIRECTION => 1)), ToolbarItem::DISPLAY_ICON ));
       	}
        
        if($course_section->get_name() != Translation :: get('CourseAdministration'))
        {
	        if ($course_section->get_visible())
	        {
	        	$toolbar->add_item(new ToolbarItem(Translation :: get('ChangeVisible'), Theme :: get_common_image_path() . 'action_visible.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CHANGE_COURSE_SECTION_VISIBILITY, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), ToolbarItem::DISPLAY_ICON ));
	       	}
	        else
	        {
	        	$toolbar->add_item(new ToolbarItem(Translation :: get('ChangeVisible'), Theme :: get_common_image_path() . 'action_invisible.png', $this->browser->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CHANGE_COURSE_SECTION_VISIBILITY, CourseSectionsTool :: PARAM_COURSE_SECTION_ID => $course_section->get_id())), ToolbarItem::DISPLAY_ICON ));
	        }
        }
        
        return $toolbar->as_html();
    
    }
}
?>