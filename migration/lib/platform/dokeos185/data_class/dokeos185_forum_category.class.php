<?php
/**
 * $Id: dokeos185_forum_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 forum_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumCategory extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'forum_category';
    
    /**
     * Dokeos185ForumCategory properties
     */
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_CAT_TITLE = 'cat_title';
    const PROPERTY_CAT_COMMENT = 'cat_comment';
    const PROPERTY_CAT_ORDER = 'cat_order';
    const PROPERTY_LOCKED = 'locked';
   
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CAT_ID, self :: PROPERTY_CAT_TITLE, self :: PROPERTY_CAT_COMMENT, self :: PROPERTY_CAT_ORDER, self :: PROPERTY_LOCKED);
    }

    /**
     * Returns the cat_id of this Dokeos185ForumCategory.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the cat_title of this Dokeos185ForumCategory.
     * @return the cat_title.
     */
    function get_cat_title()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_TITLE);
    }

    /**
     * Returns the cat_comment of this Dokeos185ForumCategory.
     * @return the cat_comment.
     */
    function get_cat_comment()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_COMMENT);
    }

    /**
     * Returns the cat_order of this Dokeos185ForumCategory.
     * @return the cat_order.
     */
    function get_cat_order()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ORDER);
    }

    /**
     * Returns the locked of this Dokeos185ForumCategory.
     * @return the locked.
     */
    function get_locked()
    {
        return $this->get_default_property(self :: PROPERTY_LOCKED);
    }

    /**
     * Check if the forum category is valid
     * @param array $array the parameters for the validation
     * @return true if the forum category is valid 
     */
    function is_valid()
    {
        if (! ($this->get_cat_title()))
        {
            $this->create_failed_element($this->get_cat_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'forum_category', 'ID' => $this->get_cat_id())));
            return false;
        }
        
        return true;
    }

    /**
     * Convert to new forum category
     * @param array $array the parameters for the conversion
     * @return the new forum category
     */
    function convert_data()
    {
        $chamilo_forum_category = new ContentObjectPublicationCategory();
        $chamilo_forum_category->set_name($this->get_cat_title());
        $chamilo_forum_category->set_course($this->get_id_reference($this->get_course()->get_code(), 'main_database.course'));
        $chamilo_forum_category->set_tool('forum');
        $chamilo_forum_category->create();
        
        //Add id references to temp table
        $this->create_id_reference($this->get_cat_id(), $chamilo_forum_category->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'forum_category', 'OLD_ID' => $this->get_cat_id(), 'NEW_ID' => $chamilo_forum_category->get_id())));
    }

	static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}

?>