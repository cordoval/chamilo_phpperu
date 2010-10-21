<?php
require_once dirname(__FILE__) . '/cas_account_connection.class.php';

/**
 * An extension of the regular Database class which
 * uses another database-connection
 */
class CasAccountDatabase extends Database
{

    /**
     * Initialiser, creates the connection and sets the database to UTF8
     */
    function initialize()
    {
        $this->set_connection(CasAccountConnection :: get_instance()->get_connection());
    }
}
?>
