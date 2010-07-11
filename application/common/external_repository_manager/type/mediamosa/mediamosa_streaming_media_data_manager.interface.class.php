<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MediamosaStreamingMediaDataManagerinterface
 *
 * @author jevdheyd
 */
interface MediamosaStreamingMediaDataManagerInterface{

    function retrieve_streaming_media_server_objects($condition = null, $order_by = null, $offset = null, $max_objects = null);

    function retrieve_streaming_media_server_object($id);

    function retrieve_streaming_media_user_quotum($user_id, $server_id);

    function retrieve_streaming_media_user_quota($user_id);

    function count_streaming_media_server_objects($condition = null);

    function create_streaming_media_server_object($mediamosa_server_object);

    function create_streaming_media_user_quotum($mediamosa_user_quotum);

    function update_streaming_media_server_object($mediamosa_server_object);

    function update_streaming_media_user_quotum($mediamosa_user_quotum);

    function delete_streaming_media_server_object($mediamosa_server_object);

    function delete_streaming_media_user_quotum($mediamosa_user_quotum);

    function delete_streaming_media_user_quota($server_id);

    function create_streaming_media_server_object_table();
}
?>
