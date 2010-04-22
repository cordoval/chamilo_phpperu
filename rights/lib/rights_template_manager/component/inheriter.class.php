<?php
/**
 * $Id: inheriter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */

class RightsTemplateManagerInheriterComponent extends RightsTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $location = $this->retrieve_location($id);
                $location->inherit();
                
                if (! $location->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLocationNotInheriting';
                }
                else
                {
                    $message = 'SelectedLocationsNotInheriting';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLocationInheriting';
                }
                else
                {
                    $message = 'SelectedLocationsInheriting';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $location->get_application(), RightsTemplateManager :: PARAM_LOCATION => $location->get_id()));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>