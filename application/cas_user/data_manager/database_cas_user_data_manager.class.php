<?php
/**
 * @package cda.datamanager
 */

require_once dirname(__FILE__) . '/../cas_user_request.class.php';
require_once dirname(__FILE__) . '/../cas_user_data_manager_interface.class.php';

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Hans De Bisschop
 */

class DatabaseCasUserDataManager extends Database implements CasUserDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('cas_user_');
    }

    function get_next_cas_user_request_id()
    {
        return $this->get_next_id(CasUserRequest :: get_table_name());
    }

    function create_cas_user_request($cas_user_request)
    {
        return $this->create($cas_user_request);
    }

    function update_cas_user_request($cas_user_request)
    {
        $condition = new EqualityCondition(CasUserRequest :: PROPERTY_ID, $cas_user_request->get_id());
        return $this->update($cas_user_request, $condition);
    }

    function delete_cas_user_request($cas_user_request)
    {
        $condition = new EqualityCondition(CasUserRequest :: PROPERTY_ID, $cas_user_request->get_id());
        return $this->delete($cas_user_request->get_table_name(), $condition);
    }

    function count_cas_user_requests($condition = null)
    {
        return $this->count_objects(CasUserRequest :: get_table_name(), $condition);
    }

    function retrieve_cas_user_request($id)
    {
        $condition = new EqualityCondition(CasUserRequest :: PROPERTY_ID, $id);
        return $this->retrieve_object(CasUserRequest :: get_table_name(), $condition, null, CasUserRequest :: CLASS_NAME);
    }

    function retrieve_cas_user_requests($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(CasUserRequest :: get_table_name(), $condition, $offset, $max_objects, $order_by, CasUserRequest :: CLASS_NAME);
    }
}
?>