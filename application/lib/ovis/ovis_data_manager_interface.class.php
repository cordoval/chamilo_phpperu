<?php
interface OvisDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function get_next_parameter_id();

    function create_parameter($parameter);

    function update_parameter($parameter);

    function delete_parameter($parameter);

    function count_parameters($conditions = null);

    function retrieve_parameter($id);

    function retrieve_parameters($condition = null, $offset = null, $count = null, $order_property = null);

    function get_next_transcoding_profile_id();

    function create_transcoding_profile($profile);

    function update_transcoding_profile($profile);

    function delete_transcoding_profile($profile);

    function count_transcoding_profiles($conditions = null);

    function retrieve_transcoding_profile($name);

    function retrieve_transcoding_profiles($condition = null, $offset = null, $count = null, $order_property = null);

    function get_next_upload_account_id();

    function create_upload_account($upload_account);

    function update_upload_account($upload_account);

    function delete_upload_account($upload_account);

    function count_upload_accounts($conditions = null);

    function retrieve_upload_account($id, $timestamp);

    function retrieve_upload_accounts($condition = null, $offset = null, $count = null, $order_property = null);

    /*needed to verify account in webservice*/
    function verify_upload_account($id, $password);

    function create_ftp_account_view();

    /* not necessary ...
        function get_next_streaming_video_ftp_account_id();
	function create_streaming_video_ftp_account($streaming_video_ftp_account);
	function update_streaming_video_ftp_account($streaming_video_ftp_account);
	function delete_streaming_video_ftp_account($streaming_video_ftp_account);
	function count_streaming_video_ftp_accounts($conditions = null);
	function retrieve_streaming_video_ftp_account($id);
	function retrieve_streaming_video_ftp_accounts($condition = null, $offset = null, $count = null, $order_property = null);
        */

    function get_next_transcoding_id();

    function create_transcoding($transcoding);

    function update_transcoding($transcoding);

    function delete_transcoding($transcoding);

    function count_transcodings($conditions = null);

    function retrieve_transcoding($id);

    function retrieve_transcodings($condition = null, $offset = null, $count = null, $order_property = null);

}
?>