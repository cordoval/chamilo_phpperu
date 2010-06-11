<?php
/**
 * $Id: templater.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_manager.component
 */
/**
 * Admin component
 */
class RightsManagerTemplaterComponent extends RightsManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        /*$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => RightsManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Rights')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('ManageRightsTemplates')));
        $trail->add_help('rights general');*/
        
        //        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
        //        {
        //            $this->display_header();
        //            $this->display_error_message(Translation :: get('NotAllowed'));
        //            $this->display_footer();
        //            exit();
        //        }
        

        $package_manager = new RightsTemplateManager($this);
        $package_manager->run();
    }
}
?>