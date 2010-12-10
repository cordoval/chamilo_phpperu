<?php
namespace common\extensions\invitation_manager;
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
     * @return InvitationParameters All parameters necessary to create an invitation
     */
//    function get_invitation_parameters();

     /**
     * Get the parameters which determine the URL of the "thing" we're inviting the user for
     * @return array All parameters necessary to create an invitation
     */
    function get_url_parameters();
    
    /**
     * Handle the users which already have an account on the system
     * @param array $user_ids An array of user ids
     */
    function process_existing_users(array $user_ids = array());
    
    /**
     * Get the location_rights_ids which will be set for the new user account 
     * @return $location_right_ids All location_right_ids necessary to create an invitation
     * format of array = key = location_id, value = array with right_ids to give on the location with location_id
     */
    function get_location_rights_ids();
    
     /**
     * Get the expiration data which determine the expiration date invitation an the user account
     * @return int unix time stamp
     */
    function get_expiration_date();
}
?>