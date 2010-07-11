<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MediamosaExternalRepositoryDataManagerinterface
 *
 * @author jevdheyd
 */
interface MediamosaExternalRepositoryDataManagerInterface{

    function retrieve_external_repository_server_objects($condition = null, $order_by = null, $offset = null, $max_objects = null);

    function retrieve_external_repository_server_object($id);

    function retrieve_external_repository_user_quotum($user_id, $server_id);

    function retrieve_external_repository_user_quota($user_id);

    function count_external_repository_server_objects($condition = null);

    function create_external_repository_server_object($mediamosa_server_object);

    function create_external_repository_user_quotum($mediamosa_user_quotum);

    function update_external_repository_server_object($mediamosa_server_object);

    function update_external_repository_user_quotum($mediamosa_user_quotum);

    function delete_external_repository_server_object($mediamosa_server_object);

    function delete_external_repository_user_quotum($mediamosa_user_quotum);

    function delete_external_repository_user_quota($server_id);

    function create_external_repository_server_object_table();
}
?>
