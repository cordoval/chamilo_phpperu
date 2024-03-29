<?php
namespace migration;

use common\libraries\Connection;
use common\libraries\Translation;

use MDB2;
use Exception;
/**
 * Class that extends the general connection class so we can use this to connect to different databases then the one of chamilo 2.0
 * @author Sven Vanpoucke
 */

class MigrationDatabaseConnection extends Connection
{
	function __construct($connection_string)
    {
        $connection = MDB2 :: connect($connection_string);
        if(MDB2 :: isError($this->connection))
        {
        	throw new Exception(Translation :: get('CouldNotConnectToPlatformDatabase'));
        }

        $this->set_connection($connection);
    }

}

?>