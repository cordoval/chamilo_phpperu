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
                if ($invitation->is_anonymous())
                {
                    $user = $this->register_new_user($invitation);

                    if ($user instanceof User)
                    {
                        $invitation->set_user_created(1);
                        $invitation->update();

                        Session :: register('_uid', $user->get_id());
                        Event :: trigger('login', 'user', array('server' => $_SERVER, 'user' => $user));

                        $url_parameters = unserialize($invitation->get_parameters());
                        Redirect :: link($url_parameters[Application :: PARAM_APPLICATION], $url_parameters, array(), false, Redirect :: TYPE_APPLICATION);
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    $parameters = array();
                    $parameters[Application :: PARAM_ACTION] = UserManager :: ACTION_REGISTER_INVITED_USER;
                    $parameters[InvitationManager :: PARAM_INVITATION_CODE] = $invitation_code;

                    Redirect :: link(UserManager :: APPLICATION_NAME, $parameters, array(), false, Redirect :: TYPE_CORE);
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

    public function register_new_user(Invitation $invitation)
    {
        $user = new User();
        $user->set_lastname(Translation :: get('User'));
        $user->set_firstname(Translation :: get('Invited'));
        $user->set_username($invitation->get_email());
        $user->set_password('PLACEHOLDER');
        $user->set_auth_source('invitation');
        $user->set_email($invitation->get_email());
        $user->set_status('5');
        $user->set_platformadmin('0');
        $user->set_disk_quota('209715200');
        $user->set_database_quota('300');
        $user->set_version_quota('20');
        $user->set_expiration_date($invitation->get_expiration_date());

        if (! $user->create())
        {
            return false;
        }
        else
        {
            return $user;
        }
    }
}

?>