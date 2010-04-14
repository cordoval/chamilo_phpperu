<?php 
/**
 * ovis
 */

require_once dirname(__FILE__) . '/ovis_data_manager.class.php';
require_once dirname(__FILE__) . '/ovis_utilities.class.php';


/**
 * This class describes a UploadAccount data object
 * @author Sven Vanpoucke
 * @author jevdheyd
 */

class UploadAccount extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * UploadAccount properties
	 */
	const DEFAULT_VALIDITY = 604800;
        const DEFAULT_VALIDITY_GRACE = 3600;
        const PROPERTY_ID = 'id';
	const PROPERTY_USERNAME = 'username';
	const PROPERTY_EXPIRES = 'expires';
	const PROPERTY_UPLOAD_PASSWORD = 'upload_password';
        const PROPERTY_HOMEDIR = 'homedir';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_USERNAME, self :: PROPERTY_EXPIRES, self :: PROPERTY_UPLOAD_PASSWORD, self :: PROPERTY_HOMEDIR);
	}

	function get_data_manager()
	{
		return OvisDataManager :: get_instance();
	}

	/**
	 * Returns the id of this UploadAccount.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this UploadAccount.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the user_id of this UploadAccount.
	 * @return the user_id.
	 */
	function get_username()
	{
		return $this->get_default_property(self :: PROPERTY_USERNAME);
	}

	/**
	 * Sets the user_id of this UploadAccount.
	 * @param user_id
	 */
	function set_username($username)
	{
		$this->set_default_property(self :: PROPERTY_USERNAME, $username);
	}

	/**
	 * Returns the expires of this UploadAccount.
	 * @return the expires.
	 */
	function get_expires()
	{
		return $this->get_default_property(self :: PROPERTY_EXPIRES);
	}

	/**
	 * Sets the expires of this UploadAccount.
	 * @param expires
	 */
	function set_expires()
	{
		$expires = time() + self::get_validity();
		$this->set_default_property(self :: PROPERTY_EXPIRES, $expires);
	}

	/**
	 * Returns the upload_password of this UploadAccount.
	 * @return the upload_password.
	 */
	function get_upload_password()
	{
		return $this->get_default_property(self :: PROPERTY_UPLOAD_PASSWORD);
	}

	/**
	 * Sets the upload_password of this UploadAccount.
	 * @param upload_password
	 */
	function set_upload_password()
	{
                $upload_password = OvisUtilities :: create_upload_pasword();
		$this->set_default_property(self :: PROPERTY_UPLOAD_PASSWORD, $upload_password);
	}

        

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

        protected static function get_validity()
        {
                return (defined('OVIS_ACCOUNT_VALIDITY')
			? OVIS_ACCOUNT_VALIDITY : self::DEFAULT_VALIDITY);
        }

        protected static function get_validity_grace()
        {
                 return (defined('OVIS_ACCOUNT_VALIDITY_GRACE')
			? OVIS_ACCOUNT_VALIDITY_GRACE : self::DEFAULT_VALIDITY_GRACE);
        }

        function set_homedir(){
                //TODO:what if upload_account :: username doesn't yet exist ??
                $this->set_default_property(self :: PROPERTY_HOMEDIR, Platformsetting :: get('upload_path', 'ovis') . '/' . $this->get_username() . '/');
        }

        function get_homedir()
        {
            return $this->get_default_property(self :: PROPERTY_HOMEDIR);
        }
        /*
         * creates and returns an upload account
         * @returns new or existing UploadAccount
         */
        static function get()
        {
                //get user
                $udm = UserDataManager :: get_instance();
                $user = $udm->retrieve_user(Session :: get_user_id());
                //check if uploadaccount exists
                $dm = OvisDataManager :: get_instance();
		$account = $dm->retrieve_upload_account($user->get_username(),
			OvisUtilities :: create_upload_pasword());
                //otherwise create upload account
                if (!$account)
                {
			$account = new UploadAccount();
                        $account->set_username($user->get_username());
                        $account->set_upload_password();
                        $account->set_expires();
                        $account->set_homedir();
			$account->create();
		}

                return $account;
        }
}

?>