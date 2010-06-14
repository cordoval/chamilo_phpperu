<?php
/**
 * $Id: creator.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */

class RightsTemplateManagerCreatorComponent extends RightsTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('RightsTemplates')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('CreateRightsTemplate')));
        $trail->add_help('rights general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
            exit();
        }
        $rights_template = new RightsTemplate();
        $rights_template->set_user_id($this->get_user_id());
        
        $form = new RightsTemplateForm(RightsTemplateForm :: TYPE_CREATE, $rights_template, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_rights_template();
            $this->redirect(Translation :: get($success ? 'RightsTemplateCreated' : 'RightsTemplateNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES));
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