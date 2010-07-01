<?php
/**
 * $Id: blog.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.blog
 *
 */
/**
 * This class represents an blog
 */
class Blog extends ContentObject implements ComplexContentObjectSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function get_allowed_types()
    {
        $allowed_types = array();
        $allowed_types[] = BlogItem :: get_type_name();
        return $allowed_types;
    }

}
?>