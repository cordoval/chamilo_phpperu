<?php
/**
 * @package application.lib.streaming_video.streaming_video_manager
 */
require_once dirname(__FILE__).'/streaming_video_manager_component.class.php';
require_once dirname(__FILE__).'/../streaming_video_data_manager.class.php';
//require_once dirname(__FILE__).'/component/parameter_browser/parameter_browser_table.class.php';
//require_once dirname(__FILE__).'/component/profile_browser/profile_browser_table.class.php';
//require_once dirname(__FILE__).'/component/upload_account_browser/upload_account_browser_table.class.php';
//require_once dirname(__FILE__).'/component/streaming_video_ftp_account_browser/streaming_video_ftp_account_browser_table.class.php';
//require_once dirname(__FILE__).'/component/streaming_video_transcoding_browser/streaming_video_transcoding_browser_table.class.php';

/**
 * A streaming_video manager
 *
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
 class StreamingVideoManager extends WebApplication
 {
 	const APPLICATION_NAME = 'streaming_video';

	const PARAM_parameter = 'parameter';
	const PARAM_DELETE_SELECTED_parameterS = 'delete_selected_parameters';

	const ACTION_DELETE_parameter = 'delete_parameter';
	const ACTION_EDIT_parameter = 'edit_parameter';
	const ACTION_CREATE_parameter = 'create_parameter';
	const ACTION_BROWSE_parameterS = 'browse_parameters';

	const PARAM_profile = 'profile';
	const PARAM_DELETE_SELECTED_profileS = 'delete_selected_profiles';

	const ACTION_DELETE_profile = 'delete_profile';
	const ACTION_EDIT_profile = 'edit_profile';
	const ACTION_CREATE_profile = 'create_profile';
	const ACTION_BROWSE_profileS = 'browse_profiles';

	const PARAM_upload_account = 'upload_account';
	const PARAM_DELETE_SELECTED_upload_accountS = 'delete_selected_upload_accounts';

	const ACTION_DELETE_upload_account = 'delete_upload_account';
	const ACTION_EDIT_upload_account = 'edit_upload_account';
	const ACTION_CREATE_upload_account = 'create_upload_account';
	const ACTION_BROWSE_upload_accountS = 'browse_upload_accounts';

	const PARAM_STREAMING_VIDEO_FTP_ACCOUNT = 'streaming_video_ftp_account';
	const PARAM_DELETE_SELECTED_STREAMING_VIDEO_FTP_ACCOUNTS = 'delete_selected_streaming_video_ftp_accounts';

	const ACTION_DELETE_STREAMING_VIDEO_FTP_ACCOUNT = 'delete_streaming_video_ftp_account';
	const ACTION_EDIT_STREAMING_VIDEO_FTP_ACCOUNT = 'edit_streaming_video_ftp_account';
	const ACTION_CREATE_STREAMING_VIDEO_FTP_ACCOUNT = 'create_streaming_video_ftp_account';
	const ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS = 'browse_streaming_video_ftp_accounts';

	const PARAM_STREAMING_VIDEO_TRANSCODING = 'streaming_video_transcoding';
	const PARAM_DELETE_SELECTED_STREAMING_VIDEO_TRANSCODINGS = 'delete_selected_streaming_video_transcodings';

	const ACTION_DELETE_STREAMING_VIDEO_TRANSCODING = 'delete_streaming_video_transcoding';
	const ACTION_EDIT_STREAMING_VIDEO_TRANSCODING = 'edit_streaming_video_transcoding';
	const ACTION_CREATE_STREAMING_VIDEO_TRANSCODING = 'create_streaming_video_transcoding';
	const ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS = 'browse_streaming_video_transcodings';


	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function StreamingVideoManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this streaming_video manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_parameterS :
				$component = StreamingVideoManagerComponent :: factory('ParametersBrowser', $this);
				break;
			case self :: ACTION_DELETE_parameter :
				$component = StreamingVideoManagerComponent :: factory('ParameterDeleter', $this);
				break;
			case self :: ACTION_EDIT_parameter :
				$component = StreamingVideoManagerComponent :: factory('ParameterUpdater', $this);
				break;
			case self :: ACTION_CREATE_parameter :
				$component = StreamingVideoManagerComponent :: factory('ParameterCreator', $this);
				break;
			case self :: ACTION_BROWSE_profileS :
				$component = StreamingVideoManagerComponent :: factory('ProfilesBrowser', $this);
				break;
			case self :: ACTION_DELETE_profile :
				$component = StreamingVideoManagerComponent :: factory('ProfileDeleter', $this);
				break;
			case self :: ACTION_EDIT_profile :
				$component = StreamingVideoManagerComponent :: factory('ProfileUpdater', $this);
				break;
			case self :: ACTION_CREATE_profile :
				$component = StreamingVideoManagerComponent :: factory('ProfileCreator', $this);
				break;
			case self :: ACTION_BROWSE_upload_accountS :
				$component = StreamingVideoManagerComponent :: factory('UploadAccountsBrowser', $this);
				break;
			case self :: ACTION_DELETE_upload_account :
				$component = StreamingVideoManagerComponent :: factory('UploadAccountDeleter', $this);
				break;
			case self :: ACTION_EDIT_upload_account :
				$component = StreamingVideoManagerComponent :: factory('UploadAccountUpdater', $this);
				break;
			case self :: ACTION_CREATE_upload_account :
				$component = StreamingVideoManagerComponent :: factory('UploadAccountCreator', $this);
				break;
			case self :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoFtpAccountsBrowser', $this);
				break;
			case self :: ACTION_DELETE_STREAMING_VIDEO_FTP_ACCOUNT :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoFtpAccountDeleter', $this);
				break;
			case self :: ACTION_EDIT_STREAMING_VIDEO_FTP_ACCOUNT :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoFtpAccountUpdater', $this);
				break;
			case self :: ACTION_CREATE_STREAMING_VIDEO_FTP_ACCOUNT :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoFtpAccountCreator', $this);
				break;
			case self :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoTranscodingsBrowser', $this);
				break;
			case self :: ACTION_DELETE_STREAMING_VIDEO_TRANSCODING :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoTranscodingDeleter', $this);
				break;
			case self :: ACTION_EDIT_STREAMING_VIDEO_TRANSCODING :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoTranscodingUpdater', $this);
				break;
			case self :: ACTION_CREATE_STREAMING_VIDEO_TRANSCODING :
				$component = StreamingVideoManagerComponent :: factory('StreamingVideoTranscodingCreator', $this);
				break;
			case self :: ACTION_BROWSE:
				$component = StreamingVideoManagerComponent :: factory('Browser', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE);
				$component = StreamingVideoManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_parameterS :

					$selected_ids = $_POST[ParameterBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_parameter);
					$_GET[self :: PARAM_parameter] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_profileS :

					$selected_ids = $_POST[ProfileBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_profile);
					$_GET[self :: PARAM_profile] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_upload_accountS :

					$selected_ids = $_POST[UploadAccountBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_upload_account);
					$_GET[self :: PARAM_upload_account] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_STREAMING_VIDEO_FTP_ACCOUNTS :

					$selected_ids = $_POST[StreamingVideoFtpAccountBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_STREAMING_VIDEO_FTP_ACCOUNT);
					$_GET[self :: PARAM_STREAMING_VIDEO_FTP_ACCOUNT] = $selected_ids;
					break;
				case self :: PARAM_DELETE_SELECTED_STREAMING_VIDEO_TRANSCODINGS :

					$selected_ids = $_POST[StreamingVideoTranscodingBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_STREAMING_VIDEO_TRANSCODING);
					$_GET[self :: PARAM_STREAMING_VIDEO_TRANSCODING] = $selected_ids;
					break;
			}

		}
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_parameters($condition)
	{
		return StreamingVideoDataManager :: get_instance()->count_parameters($condition);
	}

	function retrieve_parameters($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_parameters($condition, $offset, $count, $order_property);
	}

 	function retrieve_parameter($id)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_parameter($id);
	}

	function count_profiles($condition)
	{
		return StreamingVideoDataManager :: get_instance()->count_profiles($condition);
	}

	function retrieve_profiles($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_profiles($condition, $offset, $count, $order_property);
	}

 	function retrieve_profile($id)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_profile($id);
	}

	function count_upload_accounts($condition)
	{
		return StreamingVideoDataManager :: get_instance()->count_upload_accounts($condition);
	}

	function retrieve_upload_accounts($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_upload_accounts($condition, $offset, $count, $order_property);
	}

 	function retrieve_upload_account($id)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_upload_account($id);
	}

	function count_streaming_video_ftp_accounts($condition)
	{
		return StreamingVideoDataManager :: get_instance()->count_streaming_video_ftp_accounts($condition);
	}

	function retrieve_streaming_video_ftp_accounts($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_streaming_video_ftp_accounts($condition, $offset, $count, $order_property);
	}

 	function retrieve_streaming_video_ftp_account($id)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_streaming_video_ftp_account($id);
	}

	function count_streaming_video_transcodings($condition)
	{
		return StreamingVideoDataManager :: get_instance()->count_streaming_video_transcodings($condition);
	}

	function retrieve_streaming_video_transcodings($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_streaming_video_transcodings($condition, $offset, $count, $order_property);
	}

 	function retrieve_streaming_video_transcoding($id)
	{
		return StreamingVideoDataManager :: get_instance()->retrieve_streaming_video_transcoding($id);
	}

	// Url Creation

	function get_create_parameter_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_parameter));
	}

	function get_update_parameter_url($parameter)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_parameter,
								    self :: PARAM_parameter => $parameter->get_id()));
	}

 	function get_delete_parameter_url($parameter)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_parameter,
								    self :: PARAM_parameter => $parameter->get_id()));
	}

	function get_browse_parameters_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_parameterS));
	}

	function get_create_profile_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_profile));
	}

	function get_update_profile_url($profile)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_profile,
								    self :: PARAM_profile => $profile->get_id()));
	}

 	function get_delete_profile_url($profile)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_profile,
								    self :: PARAM_profile => $profile->get_id()));
	}

	function get_browse_profiles_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_profileS));
	}

	function get_create_upload_account_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_upload_account));
	}

	function get_update_upload_account_url($upload_account)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_upload_account,
								    self :: PARAM_upload_account => $upload_account->get_id()));
	}

 	function get_delete_upload_account_url($upload_account)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_upload_account,
								    self :: PARAM_upload_account => $upload_account->get_id()));
	}

	function get_browse_upload_accounts_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_upload_accountS));
	}

	function get_create_streaming_video_ftp_account_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_STREAMING_VIDEO_FTP_ACCOUNT));
	}

	function get_update_streaming_video_ftp_account_url($streaming_video_ftp_account)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_STREAMING_VIDEO_FTP_ACCOUNT,
								    self :: PARAM_STREAMING_VIDEO_FTP_ACCOUNT => $streaming_video_ftp_account->get_id()));
	}

 	function get_delete_streaming_video_ftp_account_url($streaming_video_ftp_account)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_STREAMING_VIDEO_FTP_ACCOUNT,
								    self :: PARAM_STREAMING_VIDEO_FTP_ACCOUNT => $streaming_video_ftp_account->get_id()));
	}

	function get_browse_streaming_video_ftp_accounts_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_STREAMING_VIDEO_FTP_ACCOUNTS));
	}

	function get_create_streaming_video_transcoding_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_STREAMING_VIDEO_TRANSCODING));
	}

	function get_update_streaming_video_transcoding_url($streaming_video_transcoding)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_STREAMING_VIDEO_TRANSCODING,
								    self :: PARAM_STREAMING_VIDEO_TRANSCODING => $streaming_video_transcoding->get_id()));
	}

 	function get_delete_streaming_video_transcoding_url($streaming_video_transcoding)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_STREAMING_VIDEO_TRANSCODING,
								    self :: PARAM_STREAMING_VIDEO_TRANSCODING => $streaming_video_transcoding->get_id()));
	}

	function get_browse_streaming_video_transcodings_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_STREAMING_VIDEO_TRANSCODINGS));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function content_object_is_published($object_id)
	{
	}

	function any_content_object_is_published($object_ids)
	{
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
	}

	function get_content_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_content_object_publications($object_id)
	{

	}

	function update_content_object_publication_id($publication_attr)
	{

	}

	function get_content_object_publication_locations($content_object)
	{

	}

	function publish_content_object($content_object, $location)
	{

	}
}
?>