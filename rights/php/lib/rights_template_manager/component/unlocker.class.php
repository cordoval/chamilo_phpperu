<?php
/**
 * $Id: unlocker.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */

class RightsTemplateManagerUnlockerComponent extends RightsTemplateManager
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
                $location->unlock();
                
                if (! $location->update())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLocationNotUnlocked';
                }
                else
                {
                    $message = 'SelectedLocationsNotUnlocked';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLocationUnlocked';
                }
                else
                {
                    $message = 'SelectedLocationsUnlocked';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $location->get_application(), RightsTemplateManager :: PARAM_LOCATION => $location->get_id()));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$ids = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
    	$location_id = $ids[0];
    	
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES,
    															  RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES)), 
    										 Translation :: get('RightsTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES,
    															  RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES,
    															  RightsTemplateManager :: PARAM_SOURCE => Request :: get(RightsTemplateManager :: PARAM_SOURCE), 
            													  RightsTemplateManager :: PARAM_LOCATION => $location_id)), 
    										 Translation :: get('RightsTemplateManagerConfigurerComponent')));									
    	$breadcrumbtrail->add_help('rights_templates_unlocker');
    }
    
	function get_additional_parameters()
    {
    	return array(RightsTemplateManager :: PARAM_LOCATION, RightsTemplateManager :: PARAM_SOURCE);
    }
}
?>