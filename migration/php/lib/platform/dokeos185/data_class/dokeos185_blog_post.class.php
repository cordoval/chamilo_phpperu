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
    const TABLE_NAME = 'blog_post';
    
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
        if (!$this->get_id_reference($this->get_author_id(), 'main_database.user') || !$this->get_blog_id() || !($this->get_title() || $this->get_full_text()) || !$this->get_date_creation())
        {
            $this->create_failed_element($this->get_post_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'blog_post', 'ID' => $this->get_post_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new blog post
     * @param array $array the parameters for the conversion
     */
    function convert_data()
    {
    	$course = $this->get_course();

        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        $new_user_id = $this->get_id_reference($this->get_author_id(), 'main_database.user');
        
        if(!$new_user_id)
        {
        	$new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }
        
        $chamilo_blog_item = new BlogItem();
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Blogs'));

        $chamilo_blog_item->set_parent_id($chamilo_category_id);
        
        if (! $this->get_title())
        {
            $chamilo_blog_item->set_title(Utilities :: truncate_string($this->get_full_text(), 20));
        }
        else
        {
            $chamilo_blog_item->set_title($this->get_title());
        }
        
        if (! $this->get_full_text())
        {
            $chamilo_blog_item->set_description($this->get_title());
        }
        else
        {
            $chamilo_blog_item->set_description($this->get_full_text());
        }
        
        $chamilo_blog_item->set_owner_id($new_user_id);
        $chamilo_blog_item->set_creation_date(strtotime($this->get_date_creation()));
        $chamilo_blog_item->set_modification_date(strtotime($this->get_date_creation()));
            
        //create announcement in database
        $chamilo_blog_item->create_all();
        
    	$parent_blog_id = $this->get_id_reference($this->get_blog_id(),  $this->get_database_name() . '.blog');
        if($parent_blog_id)
        {
        	$complex_content_object_item = $this->create_complex_content_object_item($chamilo_blog_item, $parent_blog_id, $new_user_id, strtotime($this->get_date_creation()));
        }
        
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'blog_post', 'OLD_ID' => $this->get_post_id(), 'NEW_ID' => $chamilo_blog_item->get_id())));
        $this->create_id_reference($this->get_post_id(), $chamilo_blog_item->get_id());
        $this->create_id_reference($this->get_blog_id(), $complex_content_object_item->get_id(), $this->get_database_name() . '.' . $this->get_table_name() . '.complex');
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