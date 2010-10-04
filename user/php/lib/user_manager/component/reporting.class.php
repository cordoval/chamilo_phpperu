<?php
/**
 * $Id: reporting.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */


class UserManagerReportingComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $classname = Request :: get(ReportingManager :: PARAM_TEMPLATE_NAME);

		$user_id = Request :: get(UserManager::PARAM_USER_USER_ID);
        if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $user_id))
	    {
	      	$this->display_header();
	        Display :: error_message(Translation :: get("NotAllowed"));
	        $this->display_footer();
	        exit();
	    }

        $user = $this->retrieve_user($user_id);
        $this->set_parameter(UserManager::PARAM_USER_USER_ID, $user_id);

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('user_logins_reporting_template', UserManager::APPLICATION_NAME);
        $rtv->show_all_blocks();

        $rtv->run();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_reporting');
    }
    
    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>