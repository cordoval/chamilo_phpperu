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
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES)), Translation :: get('TypeTemplates')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES)), Translation :: get('CreateTypeTemplate')));
        $trail->add_help('rights general');
        
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
}
?>