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
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>