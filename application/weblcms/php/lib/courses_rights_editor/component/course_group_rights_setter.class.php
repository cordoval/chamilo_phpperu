<?php
namespace application\weblcms;

use common\extensions\rights_editor_manager\RightsEditorManager;
use common\libraries\Request;
use common\libraries\Translation;
use common\extensions\rights_editor_manager\RightsEditorManagerBrowserComponent;

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

           // $objects = Translation :: get('Right', null, 'rights');
            $message = $success ? 'RightUpdated' : 'RightNotUpdated';

            $this->redirect(Translation :: get($message, null, 'rights'), ! $success, array_merge($this->get_parameters(), array(
                    RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, RightsEditorManagerBrowserComponent :: PARAM_TYPE => RightsEditorManagerBrowserComponent :: TYPE_GROUP)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected', null ,'rights')
));
        }
    }
}
?>