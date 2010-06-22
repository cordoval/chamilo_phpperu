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
	const PROPERTY_IMAGE = 'image';
	const PROPERTY_TAGS = 'tags';

	/**
	 * Get the additional properties
	 * @return array The property names.
	 */
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_IMAGE, self :: PROPERTY_TAGS);
	}

	/**
	 * Returns the image of this EncyclopediaItem.
	 * @return the image.
	 */
	function get_image()
	{
		return $this->get_additional_property(self :: PROPERTY_IMAGE);
	}

	/**
	 * Sets the image of this EncyclopediaItem.
	 * @param image
	 */
	function set_image($image)
	{
		$this->set_additional_property(self :: PROPERTY_IMAGE, $image);
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