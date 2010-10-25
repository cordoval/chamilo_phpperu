<?php
/**
 * $Id: location_course_group_browser_table_cell_renderer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component.location_course_group_bowser
 */
require_once dirname(__FILE__) . '/location_course_group_browser_table_column_model.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LocationCourseGroupBrowserTableCellRenderer extends ObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LocationCourseGroupBrowserTableCellRenderer($browser)
    {
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $course_group)
    {
        if ($column === LocationCourseGroupBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($course_group);
        }
        
        if (LocationCourseGroupBrowserTableColumnModel :: is_rights_column($column))
        {
            return $this->get_rights_column_value($column, $course_group);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
        	case CourseGroup :: PROPERTY_NAME:
        		return $course_group->get_name();
        	case CourseGroup :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($course_group->get_description(), 50);
                return Utilities :: truncate_string($description);
            case Translation :: get('Users') :
                return $course_group->count_members();
            case Translation :: get('Subgroups') :
                return $course_group->count_children(true);
        }
        
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($course_group)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        return $toolbar->as_html();
    }

    private function get_rights_column_value($column, $course_group)
    {
        $browser = $this->browser;
        $locations = $browser->get_locations();
        $locked_parent = $locations[0]->get_locked_parent();
        $rights = $this->browser->get_available_rights();
        $course_group_id = $course_group->get_id();
        
        $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $locations[0]->get_id())));
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Translation :: get(Utilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => CoursesRightsEditorManager :: ACTION_SET_COURSE_GROUP_RIGHTS, 'course_group_id' => $course_group_id, 'right_id' => $right_id));
                return WeblcmsRights :: get_course_group_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $course_group, $locations[0]);
            }
        }
        return '&nbsp;';
    }
    
    function render_id_cell($course_group)
    {
    	return $course_group->get_id();
    }
}
?>