<?php
/**
 * $Id: group_rights_setter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */
require_once dirname(__FILE__) . '/browser.class.php';

class RightsEditorManagerGroupRightsSetterComponent extends RightsEditorManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group = Request :: get('group_id');
        $right = Request :: get('right_id');
        
        $location = $this->get_location();
        
        if (isset($group) && isset($right) && isset($location))
        {
            $success = RightsUtilities :: invert_group_right_location($right, $group, $location->get_id());
            $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ! $success, array_merge($this->get_parameters(), array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, RightsEditorManagerBrowserComponent :: PARAM_TYPE => RightsEditorManagerBrowserComponent :: TYPE_GROUP)));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>