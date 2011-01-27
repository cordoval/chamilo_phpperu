<?php
namespace migration;
use common\libraries\Database;

/**
 * Class that extends the general database class, but needs a new connection instance because we need to connect to a different database then the one of chamilo 2.0
 * @author Sven Vanpoucke
 */

class MigrationDatabase extends Database
{

    function initialize($connection_string)
    {
        $connection = new MigrationDatabaseConnection($connection_string);
        $connection = $connection->get_connection();
        $connection->setOption('debug_handler', array(get_class($this), 'debug'));
        $connection->setCharset('ISO-8859-1');
        $this->set_connection($connection);
    }

    function record_to_object($record, $class_name)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $default_properties = array();
        $optional_properties = array();
        $object = new $class_name();

        $default_property_names = $object->get_default_property_names();
        foreach ($default_property_names as $property)
        {
            if (array_key_exists($property, $record))
            {
                $default_properties[$property] = mb_convert_encoding($record[$property], "UTF-8", "ISO-8859-1");
                unset($record[$property]);
            }
        }

        $object->set_default_properties($default_properties);

        if (count($record) > 0 && $object instanceof DataClass)
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $optional_properties[$optional_property_name] = mb_convert_encoding($optional_property_value, "UTF-8", "ISO-8859-1");
            }

            $object->set_optional_properties($optional_properties);
        }
        return $object;
    }

}

?>