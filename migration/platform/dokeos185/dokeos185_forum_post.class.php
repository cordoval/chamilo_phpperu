<?php
/**
 * $Id: dokeos185_forum_post.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_forum_post.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/forum_post/forum_post.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class presents a Dokeos185 forum_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumPost extends ImportForumPost
{
    /** 
     * Migration data manager
     */
    private static $mgdm;
    
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
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185ForumPost object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ForumPost($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_POST_ID, self :: PROPERTY_POST_TITLE, self :: PROPERTY_POST_TEXT, self :: PROPERTY_THREAD_ID, self :: PROPERTY_FORUM_ID, self :: PROPERTY_POSTER_ID, self :: PROPERTY_POSTER_NAME, self :: PROPERTY_POST_DATE, self :: PROPERTY_POST_NOTIFICATION, self :: PROPERTY_POST_PARENT_ID, self :: PROPERTY_VISIBLE);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
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
     * Retrieve all forum posts from the database
     * @param array $parameters parameters for the retrieval
     * @return array of forum posts
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
       	/*if ($parameters['del_files'] = ! 1)
            $tool_name = 'forum_post';*/
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'forum_post';
        $classname = 'Dokeos185ForumPost';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, null, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'forum_post';
        return $array;
    }

    /**
     * Check if the forum post is valid
     * @param array $array the parameters for the validation
     * @return true if the forum post is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        if (! $this->get_post_id() || ! ($this->get_post_title() || $this->get_post_text()))
        {
            $mgdm->add_failed_element($this->get_post_id(), $course->get_db_name() . '.forum_post');
            return false;
        }
        return true;
    }

    /**
     * Convert to new forum post
     * @param array $array the parameters for the conversion
     * @return the new forum post
     */
    function convert_to_lcms($array)
    {
        $mgdm = MigrationDataManager :: get_instance();
        $new_user_id = $mgdm->get_id_reference($this->get_poster_id(), 'user_user');
        $course = $array['course'];
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //forum parameters
        $lcms_forum_post = new ForumPost();
        
        // Category for forum_post already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('forums'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('ForumPost'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_forum_post->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_forum_post->set_parent_id($lcms_category_id);
        }
        
        if (! $this->get_post_title())
            $lcms_forum_post->set_title(substr($this->get_post_text(), 0, 20));
        else
            $lcms_forum_post->set_title($this->get_post_title());
        
        if (! $this->get_post_text())
            $lcms_forum_post->set_description($this->get_post_title());
        else
            $lcms_forum_post->set_description($this->get_post_text());
        
        $lcms_forum_post->set_owner_id($new_user_id);
        $lcms_forum_post->set_creation_date($mgdm->make_unix_time($this->get_post_date()));
        $lcms_forum_post->set_modification_date($mgdm->make_unix_time($this->get_post_date()));
        
        /*$parentpost = $mgdm->get_id_reference($this->get_post_parent_id(),'repository_forum_forum');
		
		if($parentpost)
			$lcms_forum_post->set_parent_post_id($parentpost);*/
        
        //if($this->get_visible() == 2)
        //$lcms_forum_post->set_state(1);
        

        //create announcement in database
        $lcms_forum_post->create_all();
        
        $mgdm->add_id_reference($this->get_post_id, $lcms_forum_post->get_id(), 'repository_forum_post');
        
    	$parent_topic = $mgdm->get_id_reference($this->get_thread_id(), 'repository_forum_thread');
        if($parent_topic)
        {
        	$wrapper = ComplexContentObjectItem :: factory('forum_post');
        	$wrapper->set_user_id($new_user_id);
        	$wrapper->set_parent($parent_topic);
        	$wrapper->set_ref($lcms_forum_post->get_id());
        	$wrapper->create();
        }
        
        /*
		//publication
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			
			if($this->get_email_sent())
				$publication->set_email_sent($this->get_email_sent());
			else
				$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();
		}
		*/
        return $lcms_forum_post;
    }

}

?>