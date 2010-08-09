<?php
/**
 * $Id: dokeos185_forum_post.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

//require_once dirname(__FILE__) . '/../../lib/import/import_forum_post.class.php';
//require_once dirname(__FILE__) . '/../../../repository/lib/content_object/forum_post/forum_post.class.php';
//require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
//require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 forum_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumPost extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'forum_post';
    
    /**
     * Dokeos185ForumPost properties
     */
    const PROPERTY_POST_ID = 'post_id';
    const PROPERTY_POST_TITLE = 'post_title';
    const PROPERTY_POST_TEXT = 'post_text';
    const PROPERTY_THREAD_ID = 'thread_id';
    const PROPERTY_FORUM_ID = 'forum_id';
    const PROPERTY_POSTER_ID = 'poster_id';
    const PROPERTY_POSTER_NAME = 'poster_name';
    const PROPERTY_POST_DATE = 'post_date';
    const PROPERTY_POST_NOTIFICATION = 'post_notification';
    const PROPERTY_POST_PARENT_ID = 'post_parent_id';
    const PROPERTY_VISIBLE = 'visible';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_POST_ID, self :: PROPERTY_POST_TITLE, self :: PROPERTY_POST_TEXT, self :: PROPERTY_THREAD_ID, self :: PROPERTY_FORUM_ID, self :: PROPERTY_POSTER_ID, self :: PROPERTY_POSTER_NAME, self :: PROPERTY_POST_DATE, self :: PROPERTY_POST_NOTIFICATION, self :: PROPERTY_POST_PARENT_ID, self :: PROPERTY_VISIBLE);
    }

    /**
     * Returns the post_id of this Dokeos185ForumPost.
     * @return the post_id.
     */
    function get_post_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_ID);
    }

    /**
     * Returns the post_title of this Dokeos185ForumPost.
     * @return the post_title.
     */
    function get_post_title()
    {
        return $this->get_default_property(self :: PROPERTY_POST_TITLE);
    }

    /**
     * Returns the post_text of this Dokeos185ForumPost.
     * @return the post_text.
     */
    function get_post_text()
    {
        return $this->get_default_property(self :: PROPERTY_POST_TEXT);
    }

    /**
     * Returns the thread_id of this Dokeos185ForumPost.
     * @return the thread_id.
     */
    function get_thread_id()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_ID);
    }

    /**
     * Returns the forum_id of this Dokeos185ForumPost.
     * @return the forum_id.
     */
    function get_forum_id()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_ID);
    }

    /**
     * Returns the poster_id of this Dokeos185ForumPost.
     * @return the poster_id.
     */
    function get_poster_id()
    {
        return $this->get_default_property(self :: PROPERTY_POSTER_ID);
    }

    /**
     * Returns the poster_name of this Dokeos185ForumPost.
     * @return the poster_name.
     */
    function get_poster_name()
    {
        return $this->get_default_property(self :: PROPERTY_POSTER_NAME);
    }

    /**
     * Returns the post_date of this Dokeos185ForumPost.
     * @return the post_date.
     */
    function get_post_date()
    {
        return $this->get_default_property(self :: PROPERTY_POST_DATE);
    }

    /**
     * Returns the post_notification of this Dokeos185ForumPost.
     * @return the post_notification.
     */
    function get_post_notification()
    {
        return $this->get_default_property(self :: PROPERTY_POST_NOTIFICATION);
    }

    /**
     * Returns the post_parent_id of this Dokeos185ForumPost.
     * @return the post_parent_id.
     */
    function get_post_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_PARENT_ID);
    }

    /**
     * Returns the visible of this Dokeos185ForumPost.
     * @return the visible.
     */
    function get_visible()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE);
    }

    /**
     * Check if the forum post is valid
     * @param array $array the parameters for the validation
     * @return true if the forum post is valid 
     */
    function is_valid()
    {
        if (! $this->get_post_id() || ! ($this->get_post_title() || $this->get_post_text()))
        {
            $this->create_failed_element($this->get_post_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'forum_post', 'ID' => $this->get_post_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new forum post
     * @param array $array the parameters for the conversion
     * @return the new forum post
     */
    function convert_data()
    {
       	$course = $this->get_course();
        
    	$new_user_id = $this->get_id_reference($this->get_poster_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        
    	if (! $new_user_id)
        {
            $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }
        
        //forum parameters
        $chamilo_forum_post = new ForumPost();
        
        // Category for forum_post already exists?
        $category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Forum'));
        $chamilo_forum_post->set_parent_id($category_id);
        
        if (! $this->get_post_title())
        {
            $chamilo_forum_post->set_title(substr($this->get_post_text(), 0, 20));
    	}
        else
        {
            $chamilo_forum_post->set_title($this->get_post_title());
        }
        
        if (! $this->get_post_text())
        {
            $chamilo_forum_post->set_description($this->get_post_title());
        }
        else
        {
            $chamilo_forum_post->set_description($this->get_post_text());
        }
        
        $chamilo_forum_post->set_owner_id($new_user_id);
        $chamilo_forum_post->set_creation_date(strtotime($this->get_post_date()));
        $chamilo_forum_post->set_modification_date(strtotime($this->get_post_date()));        

        //create announcement in database
        $chamilo_forum_post->create_all();
        
        $this->create_id_reference($this->get_post_id(), $chamilo_forum_post->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'forum_post', 'OLD_ID' => $this->get_post_id(), 'NEW_ID' => $chamilo_forum_post->get_id())));
        
    	$parent_topic = $this->get_id_reference($this->get_thread_id(),  $this->get_database_name() . '.forum_thread');
        if($parent_topic)
        {
        	$wrapper = ComplexContentObjectItem :: factory('forum_post');
        	$wrapper->set_user_id($new_user_id);
        	$wrapper->set_parent($parent_topic);
        	$wrapper->set_ref($chamilo_forum_post->get_id());
        	$wrapper->create();
        }
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