<?php
/**
 * $Id: reporting.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */


class UserManagerReportingComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {       
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
              
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        $user_id = Request :: get(UserManager::PARAM_USER_USER_ID);
        $user = $this->retrieve_user($user_id);
        $this->set_parameter(UserManager::PARAM_USER_USER_ID, $user_id);
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_USER_DETAIL, UserManager :: PARAM_USER_USER_ID => $user->get_id())), Translation :: get('DetailsOf') . ': ' . $user->get_fullname()));
        
        $trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_NAME => $classname, ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params)), Translation :: get('Report')));
        $trail->add_help('user general');
        
        $rtv = new ReportingViewer($this);
        $rtv->add_template_by_name('user_logins_reporting_template', UserManager::APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->show_all_blocks();
        
        $rtv->run();
    }
}
?>