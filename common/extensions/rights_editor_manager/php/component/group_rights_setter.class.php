<?php
/**
 * $Id: group_rights_setter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */
require_once dirname(__FILE__) . '/browser.class.php';

class RightsEditorManagerGroupRightsSetterComponent extends RightsEditorManager
{
	const PARAM_GROUP_ID = 'group_id';
	const PARAM_RIGHT_ID = 'right_id';
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $group = Request :: get(self :: PARAM_GROUP_ID);
        $right = Request :: get(self :: PARAM_RIGHT_ID);
        
        $locations = $this->get_locations();
        
        if (isset($group) && isset($right) && isset($locations) && count($locations) > 0)
        {
            $succes = true;
            foreach ($locations as $location)
            {
                $success = RightsUtilities :: invert_group_right_location($right, $group, $location->get_id());
            }
            
            $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ! $success, array_merge($this->get_parameters(), array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, RightsEditorManagerBrowserComponent :: PARAM_TYPE => RightsEditorManagerBrowserComponent :: TYPE_GROUP)));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
	        $trail->add_help('rights_editor_group_rights_setter');
	        $trail->add(new Breadcrumb($this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS)), Translation :: get('RightsEditorManagerBrowserComponent')));
	        $this->set_parameter(self :: PARAM_GROUP_ID, Request :: get(self :: PARAM_GROUP_ID));
	        $this->set_parameter(self :: PARAM_RIGHT_ID, Request :: get(self :: PARAM_RIGHT_ID));
	        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RightsEditorManagerGroupRightsSetterComponent')));
        	$this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>