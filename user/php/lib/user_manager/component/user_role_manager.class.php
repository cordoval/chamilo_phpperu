<?php
/**
 * $Id: user_role_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUserRightsTemplateManagerComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        
    	if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $user_id))
	    {
	      	$this->display_header();
	        Display :: error_message(Translation :: get("NotAllowed"));
	        $this->display_footer();
	        exit();
	    }
        
        if (! $user_id)
        {
            $this->display_header();
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
            exit();
        }
        
        $user = $this->retrieve_user($user_id);
        
        $form = new UserRightsTemplateManagerForm($user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)));
        
        if ($form->validate())
        {
            $success = $form->update_user_rights_templates();
            $this->redirect(Translation :: get($success ? 'UserRightsTemplatesChanged' : 'UserRightsTemplatesNotChanged'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
        }
        else
        {
            $this->display_header();
            
            echo sprintf(Translation :: get('ModifyRightsTemplatesForUser'), $user->get_fullname());
            
            $form->display();
            $this->display_footer();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_role_manager');
    }
    
    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>