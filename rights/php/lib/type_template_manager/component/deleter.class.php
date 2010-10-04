<?php
/**
 * $Id: deleter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */

class TypeTemplateManagerDeleterComponent extends TypeTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $rights_template = $this->retrieve_type_template($id);
                
                if (! $rights_template->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedTypeTemplateDeleted';
                }
                else
                {
                    $message = 'SelectedTypeTemplateDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedTypeTemplatesDeleted';
                }
                else
                {
                    $message = 'SelectedTypeTemplatesDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES, TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ACTION => TypeTemplateManager :: ACTION_BROWSE_TYPE_TEMPLATES));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoTypeTemplateSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES,
    															  TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ACTION => TypeTemplateManager :: ACTION_BROWSE_TYPE_TEMPLATES)), 
    										 Translation :: get('TypeTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('rights_type_templates_deleter');
    }
    
	function get_additional_parameters()
    {
    	return array(TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ID);
    }
}
?>