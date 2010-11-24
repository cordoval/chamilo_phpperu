<?php
/**
 * $Id: openid_authentication.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.authentication.openid
 */
require_once dirname(__FILE__) . '/../external_authentication.class.php';

/**
 * This class allow to login through the OpenID mechanism
 *
 */
class Openid_authentication extends ExternalAuthentication
{
	/**
	 * 
	 */
	protected function initialize_user_attributes_mapping()
	{
		
	}

	/**
	 * 
	 */
	protected function initialize_fields_to_update_at_login()
	{
		
	}

	/**
	 * 
	 */
	protected function initialize_role_attributes_mapping()
	{
		
	}

	/**
	 * @param unknown_type $user
	 * @param unknown_type $fields_to_set
	 */
	protected function set_user_attributes($user, $fields_to_set = null)
	{
		
	}

	/**
	 * @param unknown_type $user
	 */
	protected function set_user_rights($user)
	{
		
	}

	/**
	 * @param unknown_type $user
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	function check_login($user, $username, $password = null)
	{
		
	}

    /*
     * TODO
     */
}
?>