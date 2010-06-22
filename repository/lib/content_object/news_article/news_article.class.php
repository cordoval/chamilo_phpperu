<?php
/**
 * This class describes a NewsArticle data object
 *
 * @package repository.lib.content_object.news_article
 * @author Hans De Bisschop
 */

class NewsArticle extends ContentObject implements Versionable
{
	const CLASS_NAME = __CLASS__;

	/**
	 * NewsArticle properties
	 */
	const PROPERTY_HEADER = 'header';
	const PROPERTY_TAGS = 'tags';

	/**
	 * Get the additional properties
	 * @return array The property names.
	 */
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_HEADER, self :: PROPERTY_TAGS);
	}

	/**
	 * Returns the header of this NewsArticle.
	 * @return the header.
	 */
	function get_header()
	{
		return $this->get_additional_property(self :: PROPERTY_HEADER);
	}

	/**
	 * Sets the header of this NewsArticle.
	 * @param header
	 */
	function set_header($header)
	{
		$this->set_additional_property(self :: PROPERTY_HEADER, $header);
	}

	/**
	 * Returns the tags of this NewsArticle.
	 * @return the tags.
	 */
	function get_tags()
	{
		return $this->get_additional_property(self :: PROPERTY_TAGS);
	}

	/**
	 * Sets the tags of this NewsArticle.
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