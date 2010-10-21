<?php
/**
 * @author Hans De Bisschop
 */

interface CasUserDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function get_next_cas_user_request_id();

    function create_cas_user_request($cas_user_request);

    function update_cas_user_request($cas_user_request);

    function delete_cas_user_request($cas_user_request);

    function count_cas_user_requests($conditions = null);

    function retrieve_cas_user_request($id);

    function retrieve_cas_user_requests($condition = null, $offset = null, $count = null, $order_property = null);
}
?>