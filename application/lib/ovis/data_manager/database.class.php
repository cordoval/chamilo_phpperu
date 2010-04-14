<?php
/**
 * @package ovis.datamanager
 */
require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author jevdheyd
 */

require_once dirname(__FILE__).'/../parameter.class.php';
require_once dirname(__FILE__).'/../transcoding_profile.class.php';
require_once dirname(__FILE__).'/../upload_account.class.php';
//require_once dirname(__FILE__).'/../streaming_video_ftp_account.class.php';
//require_once dirname(__FILE__).'/../transcoding.class.php';

class DatabaseOvisDataManager extends OvisDataManager
{
	private $database;

	function initialize()
	{
		$aliases = array();

		$this->database = new Database($aliases);
		$this->database->set_prefix('ovis_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

        function get_next_parameter_id()
	{
		return $this->database->get_next_id(Parameter :: get_table_name());
	}

	function create_parameter($parameter)
	{
		return $this->database->create($parameter);
	}

	function update_parameter($parameter)
	{
		$condition = new EqualityCondition(Parameter :: PROPERTY_ID, $parameter->get_id());
		return $this->database->update($parameter, $condition);
	}

	function delete_parameter($parameter)
	{
		$condition = new EqualityCondition(Parameter :: PROPERTY_ID, $parameter->get_id());
		return $this->database->delete($parameter->get_table_name(), $condition);
	}

	function count_parameters($condition = null)
	{
		return $this->database->count_objects(Parameter :: get_table_name(), $condition);
	}

	function retrieve_parameter($id)
	{
		$condition = new EqualityCondition(Parameter :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Parameter :: get_table_name(), $condition);
	}

	function retrieve_parameters($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Parameter :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_transcoding_profile_id()
	{
		return $this->database->get_next_id(TranscodingProfile :: get_table_name());
	}

	function create_transcoding_profile($transcoding_profile)
	{
		return $this->database->create($transcoding_profile);
	}

	function update_transcoding_profile($transcoding_profile)
	{
		$condition = new EqualityCondition(TranscodingProfile :: PROPERTY_ID, $transcoding_profile->get_id());
		return $this->database->update($transcoding_profile, $condition);
	}

	function delete_transcoding_profile($transcoding_profile)
	{
		$condition = new EqualityCondition(TranscodingProfile :: PROPERTY_ID, $transcoding_profile->get_id());
		return $this->database->delete($transcoding_profile->get_table_name(), $condition);
	}

	function count_transcoding_profiles($condition = null)
	{
		return $this->database->count_objects(TranscodingProfile :: get_table_name(), $condition);
	}

	function retrieve_transcoding_profile($name)
	{
		$condition = new EqualityCondition(TranscodingProfile :: PROPERTY_NAME, $name);
		return $this->database->retrieve_object(TranscodingProfile :: get_table_name(), $condition);
	}

	function retrieve_transcoding_profiles($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(TranscodingProfile :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_upload_account_id()
	{
		return $this->database->get_next_id(UploadAccount :: get_table_name());
	}

	function create_upload_account($upload_account)
	{
		return $this->database->create($upload_account);
	}

	function update_upload_account($upload_account)
	{
		$condition = new EqualityCondition(UploadAccount :: PROPERTY_ID, $upload_account->get_id());
		return $this->database->update($upload_account, $condition);
	}

	function delete_upload_account($upload_account)
	{
		$condition = new EqualityCondition(UploadAccount :: PROPERTY_ID, $upload_account->get_id());
		return $this->database->delete($upload_account->get_table_name(), $condition);
	}

	function count_upload_accounts($condition = null)
	{
		return $this->database->count_objects(UploadAccount :: get_table_name(), $condition);
	}

	function retrieve_upload_account($username, $password)
	{
		$condition = new EqualityCondition(UploadAccount :: PROPERTY_USERNAME, $username);
                $condition_password = new EqualityCondition(UploadAccount :: PROPERTY_UPLOAD_PASSWORD, $password);
		$and_condition = new AndCondition($condition, $condition_password);

                return $this->database->retrieve_object(UploadAccount :: get_table_name(), $and_condition);
	}

	function retrieve_upload_accounts($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(UploadAccount :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

        function verify_upload_account($id,$password)
        {
                $condition1 = new EqualityCondition(UploadAccount :: PROPERTY_ID, $id);
                $condition2 = new EqualityCondition(UploadAccount::PROPERTY_UPLOAD_PASSWORD, $password);
                $and_condition = new AndCondition($condition1,$condition2);

               return $this->database->retrieve_record(UploadAccount::get_table_name(), $condition);
        }

        /*
         * creates mysql view for ftp account
         */
        function create_ftp_account_view(){

                $user = new User();

                //TODO: implement xml install
                //TODO: solution for name ftp_account

                $query = 'CREATE VIEW ' . $this->prefix . 'ftp_account
                        AS SELECT username AS username,
			upload_password AS password,
			CONCAT(\'' . PlatformSetting :: get('upload_path', 'ovis') . '/\', username) AS homedir
			FROM ' . $this->prefix . $user->get_table_name() . ' WHERE
			expires > UNIX_TIMESTAMP()';

                return $this->database->query($query);
        }

	function get_next_transcoding_id()
	{
		return $this->database->get_next_id(Transcoding :: get_table_name());
	}

	function create_transcoding($transcoding)
	{
		return $this->database->create($transcoding);
	}

	function update_transcoding($transcoding)
	{
		$condition = new EqualityCondition(Transcoding :: PROPERTY_ID, $transcoding->get_id());
		return $this->database->update($transcoding, $condition);
	}

	function delete_transcoding($transcoding)
	{
		$condition = new EqualityCondition(Transcoding :: PROPERTY_ID, $transcoding->get_id());
		return $this->database->delete($transcoding->get_table_name(), $condition);
	}

	function count_transcodings($condition = null)
	{
		return $this->database->count_objects(Transcoding :: get_table_name(), $condition);
	}

	function retrieve_transcoding($id)
	{
		$condition = new EqualityCondition(Transcoding :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Transcoding :: get_table_name(), $condition);
	}

	function retrieve_transcodings($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Transcoding :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
}
?>