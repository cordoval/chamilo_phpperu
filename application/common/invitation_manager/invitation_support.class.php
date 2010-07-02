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
     * @return array An array of parameters determining what we want to invite the user for
     */
    function get_invitation_parameters();
}
?>