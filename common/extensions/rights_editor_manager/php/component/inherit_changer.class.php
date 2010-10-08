<?php
/**
 * $Id: inheriter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.location_manager.component
 */

class RightsEditorManagerInheritChangerComponent extends RightsEditorManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $locations = $this->get_locations();
        $failures = 0;
        
        if (! empty($locations))
        {

            foreach ($locations as $location)
            {
                $location->switch_inherit();
                
                if (! $location->update())
                {
                    $failures ++;
                }
            }
            
            $message = $this->get_result($failures, count($locations), 'SelectedLocationNotInheriting', 'SelectedLocationsNotInheriting', 'SelectedLocationInheriting', 'SelectedLocationsInheriting');
            $this->redirect($message, ($failures ? true : false), array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
	        $trail->add_help('rights_editor_group_rights_setter');
	        $trail->add(new Breadcrumb($this->get_url(array(RightsEditorManager :: PARAM_RIGHTS_EDITOR_ACTION => RightsEditorManager :: ACTION_BROWSE_RIGHTS)), Translation :: get('RightsEditorManagerBrowserComponent')));
	        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RightsEditorManagerUserRightsSetterComponent')));
        	$this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>