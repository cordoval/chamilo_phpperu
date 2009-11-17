<?php
/**
 * $Id: course_category_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.course_category_browser
 */
require_once dirname(__FILE__) . '/course_category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course_category_table/default_course_category_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course/course_category.class.php';
require_once dirname(__FILE__) . '/../../weblcms_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseCategoryBrowserTableCellRenderer extends DefaultCourseCategoryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function CourseCategoryBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $coursecategory)
    {
        if ($column === CourseCategoryBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($coursecategory);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $coursecategory);
    }

    /**
     * Gets the action links to display
     * @param Coursecategory $coursecategory The course category for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($coursecategory)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_course_category_edit_url($coursecategory), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_course_category_delete_url($coursecategory), 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>