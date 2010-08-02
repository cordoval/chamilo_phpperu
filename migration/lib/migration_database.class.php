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
    
    /**
     * Mapper function
     * Needs to be overwritten because we need to encode all our properties to UTF8
     * @param $record
     * @param $class_name
     */
	function record_to_object($record, $class_name)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $default_properties = array();
        $optional_properties = array();

        $object = new $class_name();

        foreach ($object->get_default_property_names() as $property)
        {
            if (array_key_exists($property, $record))
            {
                $default_properties[$property] = utf8_encode($record[$property]);
                unset($record[$property]);
            }
        }

        $object->set_default_properties($default_properties);

        if (count($record) > 0 && is_a($object, DataClass :: CLASS_NAME))
        {
            foreach ($record as $optional_property_name => $optional_property_value)
            {
                $optional_properties[$optional_property_name] = utf8_encode($optional_property_value);
            }

            $object->set_optional_properties($optional_properties);
        }
        return $object;
    }
    function make_unix_time($date)
    {
        list($dat, $tim) = explode(" ", $date);
        list($y, $mo, $d) = explode("-", $dat);
        list($h, $mi, $s) = explode(":", $tim);

        return mktime($h, $mi, $s, $mo, $d, $y);
    }
}

?>