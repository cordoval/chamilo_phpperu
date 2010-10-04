<?php
/**
 * $Id: creator.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.type_template_manager.component
 */

class TypeTemplateManagerCreatorComponent extends TypeTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
            exit();
        }
        $type_template = new TypeTemplate();
        
        $form = new TypeTemplateForm(TypeTemplateForm :: TYPE_CREATE, $type_template, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_type_template();
            $this->redirect(Translation :: get($success ? 'TypeTemplateCreated' : 'TypeTemplateNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES, TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ACTION => TypeTemplateManager :: ACTION_BROWSE_TYPE_TEMPLATES));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES,
    															  TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ACTION => TypeTemplateManager :: ACTION_BROWSE_TYPE_TEMPLATES)), 
    										 Translation :: get('TypeTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('rights_type_templates_creator');
    }
}
?>