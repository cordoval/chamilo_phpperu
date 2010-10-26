<?php
namespace application\weblcms;

/**
 * $Id: unsubscribe_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms.weblcms_manager.component.unsubscribe_browser
 */
require_once dirname(__FILE__) . '/unsubscribe_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../course/course_table/default_course_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../course/course.class.php';
/**
 * Cell rendere for the unsubscribe browser table
 */
class UnsubscribeBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
    /**
     * The weblcms browser component
     */
    private $browser;

    /**
     * Constructor
     * @param WeblcmsBrowserComponent $browser
     */
    function UnsubscribeBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $course)
    {
        if ($column === UnsubscribeBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course);
        }

        // Add special features here
        switch ($column->get_name())
        {
            // Exceptions that need post-processing go here ...
        }
        return parent :: render_cell($column, $course);
    }

    /**
     * Gets the action links to display
     * @param Course $course The course for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $course = WeblcmsDataManager :: get_instance()->retrieve_course($course->get_id());
        $current_right = $course->can_user_unsubscribe($this->browser->get_user());

        if ($current_right)
        {
        	$course_unsubscription_url = $this->browser->get_course_unsubscription_url($course);
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Unsubscribe'),
	        		Theme :: get_common_image_path() . 'action_unsubscribe.png',
	        		$course_unsubscription_url,
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        else
        {
            if ($course->is_course_admin($this->browser->get_user()))
            {
                return '<span class="info_message">' . Translation :: get('UnsubscriptionAdmin') . '</span>';
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('UnsubscribeNA'),
		        		Theme :: get_common_image_path() . 'action_unsubscribe_na.png',
		        		null,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
        }

        return $toolbar->as_html();

    }
}
?>