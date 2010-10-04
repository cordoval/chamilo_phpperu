<?php
/**
 * $Id: register.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerInviterComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('InvitedUserRegistrationForm')));
        $trail->add_help('user general');

        $invitation_code = Request :: get(InvitationManager :: PARAM_INVITATION_CODE);

        if ($invitation_code)
        {
            $invitation = AdminDataManager :: get_instance()->retrieve_invitation_by_code($invitation_code);

            if ($invitation && $invitation->is_valid())
            {
                if (! $invitation->is_anonymous())
                {
                    $form = new InvitationRegistrationForm($this->get_url(array(InvitationManager :: PARAM_INVITATION_CODE => $invitation_code)), $invitation);

                    if ($form->validate())
                    {
                        $success = $form->create_user();

                        if ($success)
                        {
                            $url_parameters = unserialize($invitation->get_parameters());
                            Redirect :: link($url_parameters[Application :: PARAM_APPLICATION], $url_parameters, array(), false, Redirect :: TYPE_APPLICATION);
                        }
                        else
                        {
                            $parameters = $this->get_parameters();
                            $parameters[InvitationManager :: PARAM_INVITATION_CODE] = $invitation_code;
                            $this->redirect(Translation :: get('UsernameNotAvailable'), true, $parameters);
                        }
                    }
                    else
                    {
                        $this->display_header();
                        $form->display();
                        $this->display_footer();
                    }
                }
                else
                {
                    Display :: not_allowed();
                }
            }
            else
            {
                Display :: not_allowed();
            }
        }
        else
        {
            Display :: not_allowed();
        }
    }
}
?>