<?php
/**
 * $Id: dokeos185_forum_thread.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

//require_once dirname(__FILE__) . '/../../lib/import/import_forum_thread.class.php';
//require_once dirname(__FILE__) . '/../../../repository/lib/content_object/forum_topic/forum_topic.class.php';
//require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
//require_once 'dokeos185_item_property.class.php';
//require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 forum_thread
 *
 * @author Vanpoucke Sven
 * @author Van Wayenbergh David
 */
class Dokeos185ForumThread extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'forum_thread';
    
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
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_THREAD_ID, self :: PROPERTY_THREAD_TITLE, self :: PROPERTY_FORUM_ID, self :: PROPERTY_THREAD_REPLIES, self :: PROPERTY_THREAD_POSTER_ID, self :: PROPERTY_THREAD_POSTER_NAME, self :: PROPERTY_THREAD_VIEWS, self :: PROPERTY_THREAD_LAST_POST, self :: PROPERTY_THREAD_DATE, self :: PROPERTY_THREAD_STICKY, self :: PROPERTY_LOCKED);
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
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'forum_thread', $this->get_forum_id()));
        
        if (! $this->get_thread_id() || ! $this->get_thread_title() || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $this->create_failed_element($this->get_thread_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'forum_thread', 'ID' => $this->get_thread_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new forum thread
     * @param array $array the parameters for the conversion
     * @return the new forum thread
     */
    function convert_data()
    {
   		$new_user_id = $this->get_id_reference($this->item_property->get_insert_user_id(), 'main_database.user');
        $new_course_code = $this->get_id_reference($this->get_course()->get_code(), 'main_database.course');
        
    	if (! $new_user_id)
        {
            $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        }
        
        //forum parameters
        $chamilo_forum_topic = new ForumTopic();
        
       	$category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Forum'));
        $chamilo_forum_topic->set_parent_id($category_id);
        
        $chamilo_forum_topic->set_title($this->get_thread_title());
        $chamilo_forum_topic->set_description('');
        
        $chamilo_forum_topic->set_owner_id($new_user_id);
        $chamilo_forum_topic->set_creation_date(strtotime($this->get_thread_date()));
        $chamilo_forum_topic->set_modification_date(strtotime($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
        {
            $chamilo_forum_topic->set_state(1);
        }
        
        $chamilo_forum_topic->set_locked($this->get_locked());
            
        //create announcement in database
        $chamilo_forum_topic->create_all();
        
        //Add id references to temp table
        $this->create_id_reference($this->get_thread_id(), $chamilo_forum_topic->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'forum_thread', 'OLD_ID' => $this->get_thread_id(), 'NEW_ID' => $chamilo_forum_topic->get_id())));
		
    	$parent_forum = $this->get_id_reference($this->get_forum_id(),  $this->get_database_name() . '.forum_forum');
        if($parent_forum)
        {
        	$wrapper = ComplexContentObjectItem :: factory('forum_topic');
        	$wrapper->set_user_id($new_user_id);
        	$wrapper->set_parent($parent_forum);
        	$wrapper->set_ref($chamilo_forum_topic->get_id());
        
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