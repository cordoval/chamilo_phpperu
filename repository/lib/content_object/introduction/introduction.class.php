<?php
/**
 * $Id: introduction.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.introduction
 */
/**
 * A Introduction
 */
class Introduction extends ContentObject implements Versionable
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>