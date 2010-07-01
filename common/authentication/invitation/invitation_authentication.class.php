<?php
/** 
 * @author Hans De Bisschop
 */
class InvitationAuthentication extends Authentication implements UserRegistrationSupport
{

    /**
     * @param  User The current user object
     * @param  string The user's current password
     * @param  string The desired new password
     * @return  boolean True if changed, false if not
     * @see Authentication::change_password()
     */
    function change_password($user, $old_password, $new_password)
    {
        return false;
    }

    /**
     * @return  string Instructions for the password
     * @see Authentication::get_password_requirements()
     */
    function get_password_requirements()
    {
    
    }

    /**
     * @param  string $username
     * @param  string $password
     * @return  true
     * @see Authentication::check_login()
     */
    function check_login($user, $username, $password = null)
    {
        $invitation_code = Request :: get(InvitationManager :: PARAM_INVITATION_CODE);
        
        if ($invitation_code)
        {
            $invitation = AdminDataManager :: get_instance()->retrieve_invitation_by_code($invitation_code);
            
            if ($invitation && $invitation->is_valid())
            {
                $user = new User();
                $user->set_lastname('a');
                $user->set_firstname('a');
                $user->set_username($invitation->get_email());
                $user->set_password('a');
                $user->set_auth_source('invitation');
                $user->set_email($invitation->get_email());
                $user->set_status('5');
                $user->set_platformadmin('0');
                $user->set_disk_quota('209715200');
                $user->set_database_quota('300');
                $user->set_version_quota('20');
                $user->set_expiration_date($invitation->get_expiration_date());
                
                if ($user->create())
                {
                    $invitation->set_user_created(1);
                    $invitation->update();
                    
                    Session :: register('_uid', $user->get_id());
                    Event :: trigger('login', 'user', array('server' => $_SERVER, 'user' => $user));
        
//                    $request_uri = Session :: retrieve('request_uri');
        
//                    if ($request_uri)
//                    {
//                        $request_uris = explode("/", $request_uri);
//                        $request_uri = array_pop($request_uris);
//                        header('Location: ' . $request_uri);
//                    }
//        
//                    $login_page = PlatformSetting :: get('page_after_login');
//                    if ($login_page == 'home')
//                    {
                        header('Location: index.php');
//                    }
//                    else
//                    {
//                        header('Location: run.php?application=' . $login_page);
//                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}

?>