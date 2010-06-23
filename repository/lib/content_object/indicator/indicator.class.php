<?php
/**
 *  $Id: indicator.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.indicator
 *  @author Sven Vanpoucke
 */
/**
 * This class represents an indicator
 */
class Indicator extends ContentObject implements Versionable
{
	function get_allowed_types()
    {
        return array(Criteria :: get_type_name());
    }

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>