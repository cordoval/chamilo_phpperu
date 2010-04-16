<?php
/**
 * $Id: blog_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.blog_item
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class represents an blog_item
 */
class BlogItem extends ContentObject
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
    //Inherited
    function supports_attachments()
    {
        return true;
    }
}
?>