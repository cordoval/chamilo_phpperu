<?php
/**
 * $Id: group_rights_setter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */


class CoursesRightsEditorManagerCourseGroupRightsSetterComponent extends CoursesRightsEditorManager
{
	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group = Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP);
        $right = Request :: get('right_id');
        
        $locations = $this->get_locations();
        
        if (isset($group) && isset($right) && isset($locations) && count($locations) > 0)
        {
            $succes = true;
            foreach ($locations as $location)
            {
                $success = WeblcmsRights :: invert_course_group_right_location($right, $group, $location->get_id());
            }
            
            $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ! $success, array_merge($this->get_parameters(), array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, RightsEditorManagerBrowserComponent :: PARAM_TYPE => RightsEditorManagerBrowserComponent :: TYPE_GROUP)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>