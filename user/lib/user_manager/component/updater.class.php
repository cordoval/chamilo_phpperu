<?php
/**
 * $Id: updater.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUpdaterComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users') ));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
        $trail->add_help('user general');
        
        $id = Request :: get(UserManager :: PARAM_USER_USER_ID);
        if ($id)
        {
            $user = $this->retrieve_user($id);
            
            $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_USER_DETAIL, UserManager :: PARAM_USER_USER_ID => $id)), Translation :: get('DetailsOf') . ': ' . $user->get_fullname()));
            
            if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            $form = new UserForm(UserForm :: TYPE_EDIT, $user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));
            
            if ($form->validate())
            {
                $success = $form->update_user();
                $this->redirect(Translation :: get($success ? 'UserUpdated' : 'UserNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)), Translation :: get('Update')));
                
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>