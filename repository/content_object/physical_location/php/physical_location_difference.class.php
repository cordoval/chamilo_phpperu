<?php
namespace repository\content_object\physical_location;

use repository\ContentObjectDifference;

/**
 * $Id: physical_location_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class can be used to get the difference between physical_locations
 */
class PhysicalLocationDifference extends ContentObjectDifference
{

    function get_difference()
    {
        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = $object->get_location();
        $object_string = explode("\n", strip_tags($object_string));

        $version_string = $version->get_location();
        $version_string = explode("\n", strip_tags($version_string));

        $td = new Difference_Engine($version_string, $object_string);

        return array_merge(parent :: get_difference(), $td->getDiff());
    }
}
?>