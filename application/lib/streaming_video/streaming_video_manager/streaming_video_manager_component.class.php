<?php

/**
 * @package application.lib.streaming_video.streaming_video_manager
 * Basic functionality of a component to talk with the streaming_video application
 *
 * @author Sven Vanpoucke
 * @author jevdheyd
 */
abstract class StreamingVideoManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param StreamingVideo $streaming_video The streaming_video which
	 * provides this component
	 */
	function StreamingVideoManagerComponent($streaming_video)
	{
		parent :: __construct($streaming_video);
	}

	//Data Retrieval

	function count_parameters($condition)
	{
		return $this->get_parent()->count_parameters($condition);
	}

	function retrieve_parameters($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_parameters($condition, $offset, $count, $order_property);
	}

 	function retrieve_parameter($id)
	{
		return $this->get_parent()->retrieve_parameter($id);
	}

	function count_profiles($condition)
	{
		return $this->get_parent()->count_profiles($condition);
	}

	function retrieve_profiles($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_profiles($condition, $offset, $count, $order_property);
	}

 	function retrieve_profile($id)
	{
		return $this->get_parent()->retrieve_profile($id);
	}

	function count_upload_accounts($condition)
	{
		return $this->get_parent()->count_upload_accounts($condition);
	}

	function retrieve_upload_accounts($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_upload_accounts($condition, $offset, $count, $order_property);
	}

 	function retrieve_upload_account($id)
	{
		return $this->get_parent()->retrieve_upload_account($id);
	}

	function count_streaming_video_ftp_accounts($condition)
	{
		return $this->get_parent()->count_streaming_video_ftp_accounts($condition);
	}

	function retrieve_streaming_video_ftp_accounts($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_streaming_video_ftp_accounts($condition, $offset, $count, $order_property);
	}

 	function retrieve_streaming_video_ftp_account($id)
	{
		return $this->get_parent()->retrieve_streaming_video_ftp_account($id);
	}

	function count_streaming_video_transcodings($condition)
	{
		return $this->get_parent()->count_streaming_video_transcodings($condition);
	}

	function retrieve_streaming_video_transcodings($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->get_parent()->retrieve_streaming_video_transcodings($condition, $offset, $count, $order_property);
	}

 	function retrieve_streaming_video_transcoding($id)
	{
		return $this->get_parent()->retrieve_streaming_video_transcoding($id);
	}

	// Url Creation

	function get_create_parameter_url()
	{
		return $this->get_parent()->get_create_parameter_url();
	}

	function get_update_parameter_url($parameter)
	{
		return $this->get_parent()->get_update_parameter_url($parameter);
	}

 	function get_delete_parameter_url($parameter)
	{
		return $this->get_parent()->get_delete_parameter_url($parameter);
	}

	function get_browse_parameters_url()
	{
		return $this->get_parent()->get_browse_parameters_url();
	}

	function get_create_profile_url()
	{
		return $this->get_parent()->get_create_profile_url();
	}

	function get_update_profile_url($profile)
	{
		return $this->get_parent()->get_update_profile_url($profile);
	}

 	function get_delete_profile_url($profile)
	{
		return $this->get_parent()->get_delete_profile_url($profile);
	}

	function get_browse_profiles_url()
	{
		return $this->get_parent()->get_browse_profiles_url();
	}

	function get_create_upload_account_url()
	{
		return $this->get_parent()->get_create_upload_account_url();
	}

	function get_update_upload_account_url($upload_account)
	{
		return $this->get_parent()->get_update_upload_account_url($upload_account);
	}

 	function get_delete_upload_account_url($upload_account)
	{
		return $this->get_parent()->get_delete_upload_account_url($upload_account);
	}

	function get_browse_upload_accounts_url()
	{
		return $this->get_parent()->get_browse_upload_accounts_url();
	}

	function get_create_streaming_video_ftp_account_url()
	{
		return $this->get_parent()->get_create_streaming_video_ftp_account_url();
	}

	function get_update_streaming_video_ftp_account_url($streaming_video_ftp_account)
	{
		return $this->get_parent()->get_update_streaming_video_ftp_account_url($streaming_video_ftp_account);
	}

 	function get_delete_streaming_video_ftp_account_url($streaming_video_ftp_account)
	{
		return $this->get_parent()->get_delete_streaming_video_ftp_account_url($streaming_video_ftp_account);
	}

	function get_browse_streaming_video_ftp_accounts_url()
	{
		return $this->get_parent()->get_browse_streaming_video_ftp_accounts_url();
	}

	function get_create_streaming_video_transcoding_url()
	{
		return $this->get_parent()->get_create_streaming_video_transcoding_url();
	}

	function get_update_streaming_video_transcoding_url($streaming_video_transcoding)
	{
		return $this->get_parent()->get_update_streaming_video_transcoding_url($streaming_video_transcoding);
	}

 	function get_delete_streaming_video_transcoding_url($streaming_video_transcoding)
	{
		return $this->get_parent()->get_delete_streaming_video_transcoding_url($streaming_video_transcoding);
	}

	function get_browse_streaming_video_transcodings_url()
	{
		return $this->get_parent()->get_browse_streaming_video_transcodings_url();
	}


	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}
}
?>