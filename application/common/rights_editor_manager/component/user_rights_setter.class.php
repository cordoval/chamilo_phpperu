<?php
/**
 * $Id: user_rights_setter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */

class RightsEditorManagerUserRightsSetterComponent extends RightsEditorManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user = Request :: get('user_id');
        $right = Request :: get('right_id');
        $locations = $this->get_locations();
        
        if (isset($user) && isset($right) && isset($locations) &&  count($locations) > 0)
        {
            foreach($locations as $location)
            {
        		$success = RightsUtilities :: invert_user_right_location($right, $user, $location->get_id());
            }
            
            $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ! $success, array_merge($this->get_parameters(), array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>