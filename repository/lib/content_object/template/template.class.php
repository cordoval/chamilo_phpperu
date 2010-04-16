<?php
/**
 * $Id: template.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.template
 */
/**
 * This class represents an template
 */
class Template extends ContentObject
{
    const PROPERTY_DESIGN = 'design';
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
    function get_design()
    {
        return $this->get_additional_property(self :: PROPERTY_DESIGN);
    }

    function set_design($design)
    {
        return $this->set_additional_property(self :: PROPERTY_DESIGN, $design);
    }

    function is_versionable()
    {
        return false;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_DESIGN);
    }
}
?>