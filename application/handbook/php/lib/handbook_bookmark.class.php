<?php
namespace application\handbook;
use common\libraries\DataClass;
use common\libraries\Utilities;


require_once dirname(__FILE__).'/handbook_rights.class.php';

/**
 * This class describes a HandbookBookmark data object
 * @author Nathalie Blocry
 */
class HandbookBookmark extends DataClass
{
	const CLASS_NAME = __CLASS__;
        const TABLE_NAME = 'handbook_bookmark';

	/**
	 * HandbookBookmark properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_PUBLICATION_ID = 'publication_id';
        const PROPERTY_COMMENT = 'comment';
	

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_COMMENT);
	}

	function get_data_manager()
	{
		return HandbookDataManager :: get_instance();
	}

	/**
	 * Returns the id of this HandbookBookmark.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this HandbookBookmark.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the content_object_id of this HandbookBookmark.
	 * @return the content_object_id.
	 */
	function get_content_object_id()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
	}

	/**
	 * Sets the content_object_id of this HandbookBookmark.
	 * @param content_object_id
	 */
	function set_content_object_id($content_object_id)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
	}

	/**
	 * Returns the user_id of this HandbookBookmark.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this HandbookBookmark.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}

	/**
	 * Returns the publication_id of this HandbookBookmark.
	 * @return the publication_id.
	 */
	function get_publication_id()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
	}

	/**
	 * Sets the publication_id of this HandbookBookmark.
	 * @param publication_id
	 */
	function set_publication_id($publication_id)
	{
		$this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
	}

	/**
	 * Returns the comment of this HandbookBookmark.
	 * @return the comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}

	/**
	 * Sets the comment of this HandbookBookmark.
	 * @param comment
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
	}


	static function get_table_name()
	{
//		return Utilities::get_classname_from_namespace(Utilities :: camelcase_to_underscores(self :: CLASS_NAME));

            return self :: TABLE_NAME;
	}

        function create()
        {
            $succes = parent :: create();
            if(!$succes)
            {
                    return false;
            }
            else
            {
                  return HandbookRights :: create_location_in_handbooks_subtree($this->get_id());
            }

        }

        function delete()
        {
            $location = HandbookRights :: get_location_by_identifier_from_handbooks_subtree($this->get_id());
            if($location)
            {
                    if(!$location->remove())
                    {
                            return false;
                    }
            }
            return parent :: delete();
        }
}

?>