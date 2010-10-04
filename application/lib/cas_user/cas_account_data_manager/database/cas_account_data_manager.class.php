<?php
require_once 'cas_account_database.class.php';
require_once 'cas_account_connection.class.php';
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
    
    function count_cas_accounts()
    {
        
    }
    
    function retrieve_cas_accounts()
    {
        
    }
    
    function create_cas_account()
    {
        
    }
    
    function update_cas_account()
    {
        
    }
    
    function delete_cas_account()
    {
        
    }
}
?>