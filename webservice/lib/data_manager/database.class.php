<?php
/**
 * $Id: database.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.data_manager
 */

require_once 'MDB2.php';

/**
 * ==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Stefan Billiet
==============================================================================
 */

class DatabaseWebserviceDataManager extends WebserviceDataManager
{
    private $database;

    function initialize()
    {
        $this->database = new Database(array('webservice_registration' => 'cw', 'webservice_category' => 'cwc', 'webservice_credential' => 'cwcr'));
        $this->database->set_prefix('webservice_');
    }

    function get_database()
    {
        return $this->database;
    }

    function count_webservices($conditions = null)
    {
        return $this->database->count_objects(WebserviceRegistration :: get_table_name(), $conditions);
    }

    function truncate_webservice($webservice)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_WEBSERVICE_ID, $webservice->get_id());
        return $this->database->delete(WebserviceRegistration :: get_table_name(), $condition);
    }

    function truncate_webservice_category($webserviceCategory)
    {
        $condition = new EqualityCondition(WebserviceCategoryRegistration :: PROPERTY_WEBSERVICE_ID, $webservice->get_id());
        return $this->database->delete(WebserviceCategoryRegistration :: get_table_name(), $condition);
    }

    function truncate_webservice_credential($webserviceCredential)
    {
        $conditions[0] = new EqualityCondition(WebserviceCredential :: PROPERTY_USER_ID, $webserviceCredential->get_user_id());
        $conditions[1] = new EqualityCondition(WebserviceCredential :: PROPERTY_HASH, $webserviceCredential->get_hash());
        $condition = new AndCondition($conditions);
        return $this->database->delete(WebserviceCredential :: get_table_name(), $condition);
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    function retrieve_webservice($id)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(WebserviceRegistration :: get_table_name(), $condition);
    }

    function retrieve_webservice_by_name($name)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_NAME, $name);
        return $this->database->retrieve_object(WebserviceRegistration :: get_table_name(), $condition);
    }

    function retrieve_webservices($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(WebserviceRegistration :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_webservice_category($id)
    {
        $condition = new EqualityCondition(WebserviceCategory :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(WebserviceCategory :: get_table_name(), $condition);
    }

    function retrieve_webservice_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(WebserviceCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_webservice_credential_by_hash($hash)
    {
        $condition = new EqualityCondition(WebserviceCredential :: PROPERTY_HASH, $hash);
        return $this->database->retrieve_object(WebserviceCredential :: get_table_name(), $condition);
    }

    function retrieve_webservice_credential_by_user_id($user_id)
    {
        $condition = new EqualityCondition(WebserviceCredential :: PROPERTY_USER_ID, $user_id);
        return $this->database->retrieve_object(WebserviceCredential :: get_table_name(), $condition);
    }

    function retrieve_webservice_credentials_by_ip($ip)
    {
        $condition = new EqualityCondition(WebserviceCredential :: PROPERTY_IP, $ip);
        return $this->database->retrieve_objects(WebserviceCredential :: get_table_name(), $condition);
    }

    function delete_webservice($webservice)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_ID, $webservice->get_id());
        $bool = $this->database->delete($webservice->get_table_name(), $condition);

        $condition_subwebservices = new EqualityCondition(WebserviceRegistration :: PROPERTY_PARENT, $webservice->get_id());
        $webservices = $this->retrieve_webservices($condition_subwebservices);
        while ($ws = $webservices->next_result())
        {
            $bool = $bool & $this->delete_webservice($ws);
        }

        $this->truncate_webservice($webservice);

        return $bool;
    }

    function delete_webservice_category($webserviceCategory)
    {
        $condition = new EqualityCondition(WebserviceCategoryRegistration :: PROPERTY_ID, $webservice->get_id());
        $bool = $this->database->delete($webserviceCategory->get_table_name(), $condition);
        $this->truncate_webservice_category($webserviceCategory);

        return $bool;
    }

    function update_webservice($webservice)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_ID, $webservice->get_id());
        return $this->database->update($webservice, $condition);
    }

    function update_webservice_category($webserviceCategory)
    {
        $condition = new EqualityCondition(WebserviceCategoryRegistration :: PROPERTY_ID, $webserviceCategory->get_id());
        return $this->database->update($webserviceCategory, $condition);
    }

    function update_webservice_credential($webserviceCredential)
    {
        $condition = new EqualityCondition(WebserviceCredential :: PROPERTY_HASH, $webserviceCredential->get_hash());
        return $this->database->update($webserviceCredential, $condition);
    }

    function create_webservice($webservice)
    {
        return $this->database->create($webservice);
    }

    function create_webservice_category($webserviceCategory)
    {
        return $this->database->create($webserviceCategory);
    }

    function create_webservice_credential($webserviceCredential)
    {
        return $this->database->create($webserviceCredential);
    }

    function delete_webservice_credential($webserviceCredential)
    {
        $condition = new EqualityCondition(WebserviceCredential :: PROPERTY_USER_ID, $webserviceCredential->get_user_id());
        $bool = $this->database->delete($webserviceCredential->get_table_name(), $condition);
        $this->truncate_webservice_credential($webserviceCredential);

        return $bool;
    }

    function delete_expired_webservice_credentials()
    {
        $condition = new InequalityCondition(WebserviceCredential :: PROPERTY_END_TIME, InequalityCondition :: LESS_THAN_OR_EQUAL, time());
        $bool = $this->database->delete(WebserviceCredential :: get_table_name(), $condition);
        return $bool;
    }
}
?>