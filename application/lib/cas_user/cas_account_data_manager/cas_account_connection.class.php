<?php
/**
 * This class represents the current CAS Account database connection.
 *
 * @author Hans De Bisschop
 */

class CasAccountConnection extends Connection
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * The MDB2 Connection object.
     */
    protected $connection;

    /**
     * Constructor.
     */
    private function CasAccountConnection()
    {
        $cas_dbms = PlatformSetting :: get('dbms', CasUserManager :: APPLICATION_NAME);
        $cas_user = PlatformSetting :: get('user', CasUserManager :: APPLICATION_NAME);
        $cas_password = PlatformSetting :: get('password', CasUserManager :: APPLICATION_NAME);
        $cas_host = PlatformSetting :: get('host', CasUserManager :: APPLICATION_NAME);
        $cas_database = PlatformSetting :: get('database', CasUserManager :: APPLICATION_NAME);

        $this->connection = MDB2 :: connect($cas_dbms . '://' . $cas_user . ':' . $cas_password . '@' . $cas_host . '/' . $cas_database, array('debug' => 3));
    }

    /**
     * Returns the instance of this class.
     * @return Connection The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets the database connection.
     * @return mixed MDB2 DB Conenction.
     */
    function get_connection()
    {
        return $this->connection;
    }

    function set_option($option, $value)
    {
        $this->connection->setOption($option, $value);
    }
}
?>
