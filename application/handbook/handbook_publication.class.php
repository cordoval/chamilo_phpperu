<?php 
/**
 * handbook
 */

/**
 * This class describes a HandbookPublication data object
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookPublication extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * HandbookPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
	const PROPERTY_OWNER_ID = 'owner_id';
	const PROPERTY_PUBLISHER_ID = 'publisher_id';
	const PROPERTY_PUBLISHED = 'published';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_OWNER_ID, self :: PROPERTY_PUBLISHER_ID, self :: PROPERTY_PUBLISHED);
	}

	function get_data_manager()
	{
		return HandbookDataManager :: get_instance();
	}

	/**
	 * Returns the id of this HandbookPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this HandbookPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the content_object_id of this HandbookPublication.
	 * @return the content_object_id.
	 */
	function get_content_object_id()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
	}

	/**
	 * Sets the content_object_id of this HandbookPublication.
	 * @param content_object_id
	 */
	function set_content_object_id($content_object_id)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
	}

	/**
	 * Returns the owner_id of this HandbookPublication.
	 * @return the owner_id.
	 */
	function get_owner_id()
	{
		return $this->get_default_property(self :: PROPERTY_OWNER_ID);
	}

	/**
	 * Sets the owner_id of this HandbookPublication.
	 * @param owner_id
	 */
	function set_owner_id($owner_id)
	{
		$this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
	}

	/**
	 * Returns the publisher_id of this HandbookPublication.
	 * @return the publisher_id.
	 */
	function get_publisher_id()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
	}

	/**
	 * Sets the publisher_id of this HandbookPublication.
	 * @param publisher_id
	 */
	function set_publisher_id($publisher_id)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher_id);
	}

	/**
	 * Returns the published of this HandbookPublication.
	 * @return the published.
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the published of this HandbookPublication.
	 * @param published
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}


	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}

?>