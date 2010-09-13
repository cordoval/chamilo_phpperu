<?php
/**
 * $Id: deleter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */

class RightsTemplateManagerDeleterComponent extends RightsTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $rights_template = $this->retrieve_rights_template($id);
                
                if (! $rights_template->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedRightsTemplateDeleted';
                }
                else
                {
                    $message = 'SelectedRightsTemplateDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedRightsTemplatesDeleted';
                }
                else
                {
                    $message = 'SelectedRightsTemplatesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoRightsTemplateSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES,
    															  RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES)), 
    										 Translation :: get('RightsTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('rights_templates_deleter');
    }
    
	function get_additional_parameters()
    {
    	return array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);
    }
}
?>