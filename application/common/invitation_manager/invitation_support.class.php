<?php
/**
 * A class implements the <code>InvitationSupport</code> interface to
 * indicate that it supports the creation of invitations
 *
 * @author  Hans De Bisschop
 */
interface InvitationSupport
{

    /**
     * Get the parameters which determine the URL of the "thing" we're inviting the user for
     * @return array An array of parameters
     */
    function get_invitation_parameters();

    /**
     * Handle the users which already have an account on the system
     * @param array $user_ids An array of user ids
     */
    function process_existing_users(array $user_ids = array());
}
?>