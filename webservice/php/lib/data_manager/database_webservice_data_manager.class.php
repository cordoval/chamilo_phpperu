<?php
namespace webservice;

use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\InequalityCondition;
use common\libraries\Database;
/**
 * $Id: database_webservice_data_manager.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.data_manager
 */

require_once dirname(__FILE__) . '/../webservice_data_manager_interface.class.php';

/**
 * ==============================================================================
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Stefan Billiet
==============================================================================
 */

class DatabaseWebserviceDataManager extends Database implements WebserviceDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('webservice_');
    }

    function count_webservices($conditions = null)
    {
        return $this->count_objects(WebserviceRegistration :: get_table_name(), $conditions);
    }

    function truncate_webservice($webservice)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_WEBSERVICE_ID, $webservice->get_id());
        return $this->delete(WebserviceRegistration :: get_table_name(), $condition);
    }

    function retrieve_webservice($id)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_ID, $id);
        return $this->retrieve_object(WebserviceRegistration :: get_table_name(), $condition);
    }

    function retrieve_webservice_by_name($name)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_NAME, $name);
        return $this->retrieve_object(WebserviceRegistration :: get_table_name(), $condition);
    }

    function retrieve_webservices($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(WebserviceRegistration :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_webservice_category($id)
    {
        $condition = new EqualityCondition(WebserviceCategory :: PROPERTY_ID, $id);
        return $this->retrieve_object(WebserviceCategory :: get_table_name(), $condition);
    }

    function retrieve_webservice_categories($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(WebserviceCategory :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function delete_webservice($webservice)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_ID, $webservice->get_id());
        $bool = $this->delete($webservice->get_table_name(), $condition);

        $condition_subwebservices = new EqualityCondition(WebserviceRegistration :: PROPERTY_PARENT, $webservice->get_id());
        $webservices = $this->retrieve_webservices($condition_subwebservices);
        while ($ws = $webservices->next_result())
        {
            $bool = $bool & $this->delete_webservice($ws);
        }

        $this->truncate_webservice($webservice);

        return $bool;
    }

    function delete_webservices($condition)
    {
        return $this->delete_objects(WebserviceRegistration :: get_table_name(), $condition);
    }

    function update_webservice($webservice)
    {
        $condition = new EqualityCondition(WebserviceRegistration :: PROPERTY_ID, $webservice->get_id());
        return $this->update($webservice, $condition);
    }

    function create_webservice($webservice)
    {
        return $this->create($webservice);
    }

    function create_webservice_category($webservice_category)
    {
        return $this->create($webservice_category);
    }

}
?>