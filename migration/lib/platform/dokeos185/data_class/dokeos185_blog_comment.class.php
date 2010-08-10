<?php
/**
 * $Id: dokeos185_blog_comment.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 blog_comment
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogComment extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'blog_comment';
    
    
    /**
     * Dokeos185BlogComment properties
     */
    const PROPERTY_COMMENT_ID = 'comment_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_AUTHOR_ID = 'author_id';
    const PROPERTY_DATE_CREATION = 'date_creation';
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_POST_ID = 'post_id';
    const PROPERTY_TASK_ID = 'task_id';
    const PROPERTY_PARENT_COMMENT_ID = 'parent_comment_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_COMMENT_ID, self :: PROPERTY_TITLE, self :: PROPERTY_COMMENT, self :: PROPERTY_AUTHOR_ID, self :: PROPERTY_DATE_CREATION, self :: PROPERTY_BLOG_ID, self :: PROPERTY_POST_ID, self :: PROPERTY_TASK_ID, self :: PROPERTY_PARENT_COMMENT_ID);
    }

    /**
     * Returns the comment_id of this Dokeos185BlogComment.
     * @return the comment_id.
     */
    function get_comment_id()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT_ID);
    }

    /**
     * Returns the title of this Dokeos185BlogComment.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the comment of this Dokeos185BlogComment.
     * @return the comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the author_id of this Dokeos185BlogComment.
     * @return the author_id.
     */
    function get_author_id()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR_ID);
    }

    /**
     * Returns the date_creation of this Dokeos185BlogComment.
     * @return the date_creation.
     */
    function get_date_creation()
    {
        return $this->get_default_property(self :: PROPERTY_DATE_CREATION);
    }

    /**
     * Returns the blog_id of this Dokeos185BlogComment.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }

    /**
     * Returns the post_id of this Dokeos185BlogComment.
     * @return the post_id.
     */
    function get_post_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_ID);
    }

    /**
     * Returns the task_id of this Dokeos185BlogComment.
     * @return the task_id.
     */
    function get_task_id()
    {
        return $this->get_default_property(self :: PROPERTY_TASK_ID);
    }

    /**
     * Returns the parent_comment_id of this Dokeos185BlogComment.
     * @return the parent_comment_id.
     */
    function get_parent_comment_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_COMMENT_ID);
    }

    /**
     * Check if the blog comment is valid
     */
    function is_valid()
    {
    	$blog_publication_id = $this->get_id_reference($this->get_blog_id(),  $this->get_database_name() . '.blog.publication');
    	$complex_blog_post_id = $this->get_id_reference($this->get_post_id(),  $this->get_database_name() . '.blog_post.complex');
    	
    	if(!$this->get_blog_id() || !$this->get_post_id() || $this->get_task_id() || !($this->get_title() || $this->get_comment()) || !$this->get_date_creation() || !$blog_publication_id || !$complex_blog_post_id)
    	{
            $this->create_failed_element($this->get_comment_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'blog_comment', 'ID' => $this->get_comment_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new blog comment
     */
    function convert_data()
    {
    	$course = $this->get_course();
		$date = strtotime($this->get_date_creation());
		
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        $new_user_id = $this->get_id_reference($this->get_author_id(), 'main_database.user');
        
        if(!$new_user_id)
        {
        	$new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }
        
        $chamilo_feedback = new Feedback();
        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Blogs'));

        $chamilo_feedback->set_parent_id($chamilo_category_id);
        
        if (! $this->get_title())
        {
            $chamilo_feedback->set_title(Utilities :: truncate_string($this->get_comment(), 20));
        }
        else
        {
            $chamilo_feedback->set_title($this->get_title());
        }
        
        if (! $this->get_comment())
        {
            $chamilo_feedback->set_description($this->get_title());
        }
        else
        {
            $chamilo_feedback->set_description($this->get_comment());
        }
        
        $chamilo_feedback->set_owner_id($new_user_id);
        $chamilo_feedback->set_creation_date($date);
        $chamilo_feedback->set_modification_date($date);
        $chamilo_feedback->set_icon(Feedback :: ICON_INFORMATIVE);
        
        //create announcement in database
        $chamilo_feedback->create_all();
        
        $blog_publication_id = $this->get_id_reference($this->get_blog_id(),  $this->get_database_name() . '.blog.publication');
        $complex_blog_post_id = $this->get_id_reference($this->get_post_id(),  $this->get_database_name() . '.blog_post.complex');
        
        $this->create_feedback($chamilo_feedback, $blog_publication_id, $complex_blog_post_id, $date, $date);
        
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'blog_comment', 'OLD_ID' => $this->get_comment_id(), 'NEW_ID' => $chamilo_feedback->get_id())));
        $this->create_id_reference($this->get_comment_id(), $chamilo_feedback->get_id());

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