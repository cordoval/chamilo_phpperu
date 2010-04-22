<?php
/**
 * $Id: user_role_manager.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUserRightsTemplateManagerComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users') ));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
        $trail->add_help('user general');
        
        $user_id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if (! $user_id)
        {
            $this->display_header($trail);
            $this->display_error_message('NoObjectSelected');
            $this->display_footer();
            exit();
        }
        
        $user = $this->retrieve_user($user_id);
        
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)), $user->get_fullname()));
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)), Translation :: get('ModifyUserRightsTemplates')));
        
        $form = new UserRightsTemplateManagerForm($user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)));
        
        if ($form->validate())
        {
            $success = $form->update_user_rights_templates();
            $this->redirect(Translation :: get($success ? 'UserRightsTemplatesChanged' : 'UserRightsTemplatesNotChanged'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
        }
        else
        {
            $this->display_header($trail);
            
            echo sprintf(Translation :: get('ModifyRightsTemplatesForUser'), $user->get_fullname());
            
            $form->display();
            $this->display_footer();
        }
    }
}
?>