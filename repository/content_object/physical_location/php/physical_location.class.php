<?php
namespace repository\content_object\physical_location;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: physical_location.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.physical_location
 */
/**
 * This class represents an physical_location
 */
class PhysicalLocation extends ContentObject implements Versionable
{
    const PROPERTY_LOCATION = 'location';
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

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