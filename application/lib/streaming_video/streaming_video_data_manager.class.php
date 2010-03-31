<?php
/**
 *	This is a skeleton for a data manager for the StreamingVideo Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *  @author Sven Vanpoucke
 *	@author jevdheyd
 */
abstract class StreamingVideoDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function StreamingVideoDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return StreamingVideoDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.Utilities :: camelcase_to_underscores($type).'.class.php';
			$class = $type.'StreamingVideoDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	abstract function initialize();
	abstract function create_storage_unit($name,$properties,$indexes);

	abstract function get_next_parameter_id();
	abstract function create_parameter($parameter);
	abstract function update_parameter($parameter);
	abstract function delete_parameter($parameter);
	abstract function count_parameters($conditions = null);
	abstract function retrieve_parameter($id);
	abstract function retrieve_parameters($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function get_next_transcoding_profile_id();
	abstract function create_transcoding_profile($profile);
	abstract function update_transcoding_profile($profile);
	abstract function delete_transcoding_profile($profile);
	abstract function count_transcoding_profiles($conditions = null);
	abstract function retrieve_transcoding_profile($name);
	abstract function retrieve_transcoding_profiles($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function get_next_upload_account_id();
	abstract function create_upload_account($upload_account);
	abstract function update_upload_account($upload_account);
	abstract function delete_upload_account($upload_account);
	abstract function count_upload_accounts($conditions = null);
	abstract function retrieve_upload_account($id,$timestamp);
	abstract function retrieve_upload_accounts($condition = null, $offset = null, $count = null, $order_property = null);
        /*needed to verify account in webservice*/
        abstract function verify_upload_account($id,$password);

        abstract function create_ftp_account_view();
	/* not necessary ...
        abstract function get_next_streaming_video_ftp_account_id();
	abstract function create_streaming_video_ftp_account($streaming_video_ftp_account);
	abstract function update_streaming_video_ftp_account($streaming_video_ftp_account);
	abstract function delete_streaming_video_ftp_account($streaming_video_ftp_account);
	abstract function count_streaming_video_ftp_accounts($conditions = null);
	abstract function retrieve_streaming_video_ftp_account($id);
	abstract function retrieve_streaming_video_ftp_accounts($condition = null, $offset = null, $count = null, $order_property = null);
        */

	abstract function get_next_transcoding_id();
	abstract function create_transcoding($streaming_video_transcoding);
	abstract function update_transcoding($streaming_video_transcoding);
	abstract function delete_transcoding($streaming_video_transcoding);
	abstract function count_transcodings($conditions = null);
	abstract function retrieve_transcoding($id);
	abstract function retrieve_transcodings($condition = null, $offset = null, $count = null, $order_property = null);

}
?>