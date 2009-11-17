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
        $rtv = new ReportingTemplateViewer($this);
        
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);
        
        $params = Reporting :: get_params($this);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
        
        $user = $this->retrieve_user($params[ReportingManager :: PARAM_USER_ID]);
        
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_USER_DETAIL, UserManager :: PARAM_USER_USER_ID => $user->get_id())), Translation :: get('DetailsOf') . ': ' . $user->get_fullname()));
        
        $trail->add(new Breadcrumb($this->get_url(array(ReportingManager :: PARAM_TEMPLATE_NAME => $classname, ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params)), Translation :: get('Report')));
        $trail->add_help('user general');
        
        $this->display_header($trail);
        
        $rtv->show_reporting_template_by_name($classname, $params);
        
        $this->display_footer();
    }
}
?>