<?php
/**
 * $Id: dokeos185_blog.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 blog
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Blog extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'blog';
    
    /**
     * Dokeos185Blog properties
     */
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_BLOG_NAME = 'blog_name';
    const PROPERTY_BLOG_SUBTITLE = 'blog_subtitle';
    const PROPERTY_DATE_CREATION = 'date_creation';
    const PROPERTY_VISIBILITY = 'visibility';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_BLOG_ID, self :: PROPERTY_BLOG_NAME, self :: PROPERTY_BLOG_SUBTITLE, self :: PROPERTY_DATE_CREATION, self :: PROPERTY_VISIBILITY);
    }

    /**
     * Returns the blog_id of this Dokeos185Blog.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the blog_name of this Dokeos185Blog.
     * @return the blog_name.
     */
    function get_blog_name()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_NAME);
    }

    /**
     * Returns the blog_subtitle of this Dokeos185Blog.
     * @return the blog_subtitle.
     */
    function get_blog_subtitle()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_SUBTITLE);
    }

    /**
     * Returns the date_creation of this Dokeos185Blog.
     * @return the date_creation.
     */
    function get_date_creation()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
    }

    /**
     * Returns the visibility of this Dokeos185Blog.
     * @return the visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Check if the blog is valid
     * @return true if the blog is valid 
     */
    function is_valid()
    {
        if (! $this->get_blog_id() || ! ($this->get_blog_name() || $this->get_blog_subtitle()) || !$this->get_date_creation())
        {
            $this->create_failed_element($this->get_blog_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'blog', 'ID' => $this->get_blog_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new blog
     * @return the new blog
     */
    function convert_data()
    {
    	$course = $this->get_course();

        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        
        $chamilo_blog = new Blog();
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Blogs'));

        $chamilo_blog->set_parent_id($chamilo_category_id);
        
        if (! $this->get_blog_name())
        {
            $chamilo_blog->set_title(Utilities :: truncate_string($this->get_blog_subtitle(), 20));
        }
        else
        {
            $chamilo_blog->set_title($this->get_blog_name());
        }
        
        if (! $this->get_blog_subtitle())
        {
            $chamilo_blog->set_description($this->get_blog_name());
        }
        else
        {
            $chamilo_blog->set_description($this->get_blog_subtitle());
        }
        
        $chamilo_blog->set_owner_id($new_user_id);
        $chamilo_blog->set_creation_date(strtotime($this->get_date_creation()));
        $chamilo_blog->set_modification_date(strtotime($this->get_date_creation()));
        
        if ($this->get_visibility() == 2)
        {
            $chamilo_blog->set_state(1);
        }
            
        //create announcement in database
        $chamilo_blog->create_all();
        
        $this->create_publication($chamilo_blog, $new_course_code, $new_user_id, 'blog');
        
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'blog', 'OLD_ID' => $this->get_blog_id(), 'NEW_ID' => $chamilo_blog->get_id())));
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