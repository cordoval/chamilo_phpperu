<?php
/**
 * $Id: dokeos185_forum_thread.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_forum_thread.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/forum_topic/forum_topic.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once 'dokeos185_item_property.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class presents a Dokeos185 forum_thread
 *
 * @author Vanpoucke Sven
 * @author Van Wayenbergh David
 */
class Dokeos185ForumThread extends Dokeos185MigrationDataClass
{
    /** 
     * Migration data manager
     */
    private static $mgdm;
    
    private $item_property;
    
    /**
     * Dokeos185ForumThread properties
     */
    const PROPERTY_THREAD_ID = 'thread_id';
    const PROPERTY_THREAD_TITLE = 'thread_title';
    const PROPERTY_FORUM_ID = 'forum_id';
    const PROPERTY_THREAD_REPLIES = 'thread_replies';
    const PROPERTY_THREAD_POSTER_ID = 'thread_poster_id';
    const PROPERTY_THREAD_POSTER_NAME = 'thread_poster_name';
    const PROPERTY_THREAD_VIEWS = 'thread_views';
    const PROPERTY_THREAD_LAST_POST = 'thread_last_post';
    const PROPERTY_THREAD_DATE = 'thread_date';
    const PROPERTY_THREAD_STICKY = 'thread_sticky';
    const PROPERTY_LOCKED = 'locked';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185ForumThread object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ForumThread($defaultProperties = array ())
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
        return array(self :: PROPERTY_THREAD_ID, self :: PROPERTY_THREAD_TITLE, self :: PROPERTY_FORUM_ID, self :: PROPERTY_THREAD_REPLIES, self :: PROPERTY_THREAD_POSTER_ID, self :: PROPERTY_THREAD_POSTER_NAME, self :: PROPERTY_THREAD_VIEWS, self :: PROPERTY_THREAD_LAST_POST, self :: PROPERTY_THREAD_DATE, self :: PROPERTY_THREAD_STICKY, self :: PROPERTY_LOCKED);
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
     * Returns the thread_id of this Dokeos185ForumThread.
     * @return the thread_id.
     */
    function get_thread_id()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_ID);
    }

    /**
     * Returns the thread_title of this Dokeos185ForumThread.
     * @return the thread_title.
     */
    function get_thread_title()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_TITLE);
    }

    /**
     * Returns the forum_id of this Dokeos185ForumThread.
     * @return the forum_id.
     */
    function get_forum_id()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_ID);
    }

    /**
     * Returns the thread_replies of this Dokeos185ForumThread.
     * @return the thread_replies.
     */
    function get_thread_replies()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_REPLIES);
    }

    /**
     * Returns the thread_poster_id of this Dokeos185ForumThread.
     * @return the thread_poster_id.
     */
    function get_thread_poster_id()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_POSTER_ID);
    }

    /**
     * Returns the thread_poster_name of this Dokeos185ForumThread.
     * @return the thread_poster_name.
     */
    function get_thread_poster_name()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_POSTER_NAME);
    }

    /**
     * Returns the thread_views of this Dokeos185ForumThread.
     * @return the thread_views.
     */
    function get_thread_views()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_VIEWS);
    }

    /**
     * Returns the thread_last_post of this Dokeos185ForumThread.
     * @return the thread_last_post.
     */
    function get_thread_last_post()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_LAST_POST);
    }

    /**
     * Returns the thread_date of this Dokeos185ForumThread.
     * @return the thread_date.
     */
    function get_thread_date()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_DATE);
    }

    /**
     * Returns the thread_sticky of this Dokeos185ForumThread.
     * @return the thread_sticky.
     */
    function get_thread_sticky()
    {
        return $this->get_default_property(self :: PROPERTY_THREAD_STICKY);
    }

    /**
     * Returns the locked of this Dokeos185ForumThread.
     * @return the locked.
     */
    function get_locked()
    {
        return $this->get_default_property(self :: PROPERTY_LOCKED);
    }

    /**
     * Check if the forum thread is valid
     * @param array $array the parameters for the validation
     * @return true if the forum thread is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $old_mgdm = $array['old_mgdm'];
        $mgdm = MigrationDataManager :: get_instance();
        $this->item_property = $old_mgdm->get_item_property($course->get_db_name(), 'forum_thread', $this->get_thread_id());
        
        if (! $this->get_thread_id() || ! $this->get_thread_title() || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $mgdm->add_failed_element($this->get_thread_id(), $course->get_db_name() . '.forum_thread');
            return false;
        }
        return true;
    }

    /**
     * Convert to new forum thread
     * @param array $array the parameters for the conversion
     * @return the new forum thread
     */
    function convert_data
    {
        $mgdm = MigrationDataManager :: get_instance();
        $new_user_id = $mgdm->get_id_reference($this->item_property->get_insert_user_id(), 'user_user');
        $course = $array['course'];
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //forum parameters
        $lcms_forum_topic = new ForumTopic();
        
        // Category for announcements already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('forums'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('ForumThread'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_forum_topic->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_forum_topic->set_parent_id($lcms_category_id);
        }
        
        $lcms_forum_topic->set_title($this->get_thread_title());
        
        $lcms_forum_topic->set_description('...');
        
        $lcms_forum_topic->set_owner_id($new_user_id);
        $lcms_forum_topic->set_creation_date($mgdm->make_unix_time($this->get_thread_date()));
        $lcms_forum_topic->set_modification_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
            $lcms_forum_topic->set_state(1);
        
        $lcms_forum_topic->set_locked($this->get_locked());
            
        //create announcement in database
        $lcms_forum_topic->create_all();
        
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_thread_id(), $lcms_forum_topic->get_id(), 'repository_forum_thread');
        
        $parent_forum = $mgdm->get_id_reference($this->get_forum_id(), 'repository_forum');
        if($parent_forum)
        {
        	$wrapper = ComplexContentObjectItem :: factory('forum_topic');
        	$wrapper->set_user_id($new_user_id);
        	$wrapper->set_parent($parent_forum);
        	$wrapper->set_ref($lcms_forum_topic->get_id());
        	if($this->get_thread_sticky())
        	{
        		$wrapper->set_type(1);
        	}
        	else 
        	{
        		$wrapper->set_type(0);
        	}
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
        return $lcms_forum_topic;
    }

    /**
     * Retrieve all forum threads from the database
     * @param array $parameters parameters for the retrieval
     * @return array of forum threads
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'forum_thread';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'forum_thread';
        $classname = 'Dokeos185ForumThread';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'forum_thread';
        return $array;
    }
}

?>