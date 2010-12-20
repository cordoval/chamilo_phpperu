<?php
namespace webservice;
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

    function retrieve_webservice($id);

    function retrieve_webservice_by_name($name);

    function retrieve_webservices($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_webservice_category($id);

    function retrieve_webservice_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function delete_webservice($webservice);

    function update_webservice($webservice);

    function create_webservice($webservice);

    function create_webservice_category($webservice_category);

}
?>