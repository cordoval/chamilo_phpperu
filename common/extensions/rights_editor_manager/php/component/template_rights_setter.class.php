<?php
/**
 * $Id: template_rights_setter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */

class RightsEditorManagerTemplateRightsSetterComponent extends RightsEditorManager
{
	const PARAM_TEMPLATE_ID = 'template_id';
	const PARAM_RIGHT_ID = 'right_id';
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $template = Request :: get(self :: PARAM_TEMPLATE_ID);
        $right = Request :: get(self :: PARAM_RIGHT_ID);
        $locations = $this->get_locations();
        
        if (isset($template) && isset($right) && isset($locations) &&  count($locations) > 0)
        {
            foreach($locations as $location)
            {
        		$success = RightsUtilities :: invert_rights_template_right_location($right, $template, $location->get_id());
            }
            
            $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ! $success, array_merge($this->get_parameters(), 
                        array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, 
                              RightsEditorManagerBrowserComponent :: PARAM_TYPE => RightsEditorManagerBrowserComponent :: TYPE_TEMPLATE)));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
	        $trail->add_help('rights_editor_group_rights_setter');
	        $trail->add(new Breadcrumb($this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS, 
	                    RightsEditorManagerBrowserComponent :: PARAM_TYPE => RightsEditorManagerBrowserComponent :: TYPE_TEMPLATE)), Translation :: get('RightsEditorManagerBrowserComponent')));
	        $this->set_parameter(self :: PARAM_TEMPLATE_ID, Request :: get(self :: PARAM_TEMPLATE_ID));
	        $this->set_parameter(self :: PARAM_RIGHT_ID, Request :: get(self :: PARAM_RIGHT_ID));
	        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RightsEditorManagerTemplateRightsSetterComponent')));
        	$this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>