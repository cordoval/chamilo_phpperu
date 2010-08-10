<?php
/**
 * This class describes a EncyclopediaItem data object
 *
 * @package repository.lib.content_object.encyclopedia_item
 * @author Hans De Bisschop
 */

class EncyclopediaItem extends ContentObject implements Versionable
{
	const CLASS_NAME = __CLASS__;

	/**
	 * EncyclopediaItem properties
	 */
	const PROPERTY_TAGS = 'tags';
	
	const ATTACHMENT_IMAGE = 'image';

	/**
	 * Get the additional properties
	 * @return array The property names.
	 */
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_TAGS);
	}
	
	function get_image($only_return_id = false)
	{
	    $image = array_shift($this->get_images());
	    
	    if (is_null($image))
	    {
	        return $only_return_id ? $image : false;
	    }
	    else
	    {
	        return $only_return_id ? $image->get_id() : $image;
	    }
	}

	/**
	 * Returns the image of this EncyclopediaItem.
	 * @return Array.
	 */
	function get_images()
	{
	   return $this->get_attached_content_objects(self :: ATTACHMENT_IMAGE); 
	}

	/**
	 * Sets the image of this EncyclopediaItem.
	 * @param Array image
	 */
	function set_images($images = array())
	{
	    $this->truncate_attachments(self :: ATTACHMENT_IMAGE);
	    $this->attach_content_objects($images, self :: ATTACHMENT_IMAGE);
	}

	/**
	 * Returns the tags of this EncyclopediaItem.
	 * @return the tags.
	 */
	function get_tags()
	{
		return $this->get_additional_property(self :: PROPERTY_TAGS);
	}

	/**
	 * Sets the tags of this EncyclopediaItem.
	 * @param tags
	 */
	function set_tags($tags)
	{
		$this->set_additional_property(self :: PROPERTY_TAGS, $tags);
	}


	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>