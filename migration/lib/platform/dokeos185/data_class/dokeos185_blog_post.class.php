<?php
/**
 * $Id: dokeos185_blog_post.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 blog_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogPost extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'blog';
    
    /**
     * Dokeos185BlogPost properties
     */
    const PROPERTY_POST_ID = 'post_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_FULL_TEXT = 'full_text';
    const PROPERTY_DATE_CREATION = 'date_creation';
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_AUTHOR_ID = 'author_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_POST_ID, self :: PROPERTY_TITLE, self :: PROPERTY_FULL_TEXT, self :: PROPERTY_DATE_CREATION, self :: PROPERTY_BLOG_ID, self :: PROPERTY_AUTHOR_ID);
    }

    /**
     * Returns the post_id of this Dokeos185BlogPost.
     * @return the post_id.
     */
    function get_post_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_ID);
    }

    /**
     * Returns the title of this Dokeos185BlogPost.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the full_text of this Dokeos185BlogPost.
     * @return the full_text.
     */
    function get_full_text()
    {
        return $this->get_default_property(self :: PROPERTY_FULL_TEXT);
    }

    /**
     * Returns the date_creation of this Dokeos185BlogPost.
     * @return the date_creation.
     */
    function get_date_creation()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
    }

    /**
     * Returns the blog_id of this Dokeos185BlogPost.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the author_id of this Dokeos185BlogPost.
     * @return the author_id.
     */
    function get_author_id()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR_ID);
    }

    /**
     * Check if the blog post is valid
     */
    function is_valid()
    {
    	return false;
    }

    /**
     * Convert to new blog post
     * @param array $array the parameters for the conversion
     */
    function convert_data()
    {
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