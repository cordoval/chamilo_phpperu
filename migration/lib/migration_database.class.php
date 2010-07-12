<?php

/**
 * Class that extends the general database class, but needs a new connection instance because we need to connect to a different database then the one of chamilo 2.0
 * @author Sven Vanpoucke
 */

class MigrationDatabase Extends Database
{
	function initialize($connection_string)
    {
        $connection = new MigrationDatabaseConnection($connection_string);
        $connection = $connection->get_connection();
        $connection->setOption('debug_handler', array(get_class($this), 'debug'));
        $connection->setCharset('utf8');
        
        $this->set_connection($connection);
    }
}

?>