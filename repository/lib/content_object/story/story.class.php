<?php
/**
 * This class describes a Story data object
 *
 * @package repository.lib.content_object.story
 * @author Hans De Bisschop
 */

class Story extends ContentObject
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>