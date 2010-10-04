<?php
/**
 * @package group.lib
 *
 * This is an interface for a data manager for the Webservice application.
 * Data managers must implement this class.
 *
 * @author Hans De Bisschop
 */
interface WebserviceDataManagerInterface
{

    function initialize();

    function count_webservices($conditions = null);

    function truncate_webservice($id);

    function truncate_webservice_credential($webserviceCredential);

    function retrieve_webservice($id);

    function retrieve_webservice_by_name($name);

    function retrieve_webservices($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_webservice_category($id);

    function retrieve_webservice_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_webservice_credential_by_hash($hash);

    function retrieve_webservice_credentials_by_ip($ip);

    function retrieve_webservice_credential_by_user_id($user_id);

    function create_storage_unit($name, $properties, $indexes);

    function delete_webservice($webservice);

    function delete_webservice_category($webserviceCategory);

    function delete_webservice_credential($webserviceCredential);

    function delete_expired_webservice_credentials();

    function update_webservice($webservice);

    function update_webservice_category($webserviceCategory);

    function create_webservice($webservice);

    function create_webservice_category($webserviceCategory);

    function create_webservice_credential($webserviceCredential);

}
?>