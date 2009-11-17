<?php
/**
 * $Id: physical_location.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class represents an physical_location
 */
class PhysicalLocation extends ContentObject
{
    const PROPERTY_LOCATION = 'location';

    function get_location()
    {
        return $this->get_additional_property(self :: PROPERTY_LOCATION);
    }

    function set_location($location)
    {
        return $this->set_additional_property(self :: PROPERTY_LOCATION, $location);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOCATION);
    }
}
?>