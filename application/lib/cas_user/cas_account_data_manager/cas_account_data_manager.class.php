<?php
require_once dirname(__FILE__) . '/cas_account_database.class.php';
require_once dirname(__FILE__) . '/cas_account_connection.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Hans De Bisschop
==============================================================================
 */

class CasAccountDataManager
{
    /**
     * @var CasAccountDataManager
     */
    private static $instance;

    /**
     * @var CasAccountDatabase
     */
    private $database;

    protected function CasAccountDataManager()
    {
        $this->initialize();
    }

    /**
     * @return CasAccountDataManager
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new CasAccountDataManager();
        }
        return self :: $instance;
    }

    /**
     * Initializes the connection
     */
    function initialize()
    {
        $this->database = new CasAccountDatabase();
    }

    /**
     * @return CasAccountDatabase
     */
    function get_database()
    {
        return $this->database;
    }

    function count_cas_accounts($condition = null)
    {
        return $this->database->count_objects(CasAccount :: get_table_name(), $condition);
    }

    function retrieve_cas_accounts($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(CasAccount :: get_table_name(), $condition, $offset, $max_objects, $order_by, CasAccount :: CLASS_NAME);
    }

    function retrieve_cas_account($id)
    {
        $condition = new EqualityCondition(CasAccount :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(CasAccount :: get_table_name(), $condition, null, CasAccount :: CLASS_NAME);
    }

    function create_cas_account($cas_account)
    {
        return $this->database->create($cas_account);
    }

    function update_cas_account($cas_account)
    {
        $condition = new EqualityCondition(CasAccount :: PROPERTY_ID, $cas_account->get_id());
        return $this->database->update($cas_account, $condition);
    }

    function delete_cas_account($cas_account)
    {
        $condition = new EqualityCondition(CasAccount :: PROPERTY_ID, $cas_account->get_id());
        return $this->database->delete($cas_account->get_table_name(), $condition);
    }
}
?>