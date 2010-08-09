<?php
/**
 * $Id: dokeos185_forum_forum.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

//require_once dirname(__FILE__) . '/../../lib/import/import_forum_forum.class.php';
//require_once dirname(__FILE__) . '/../../../repository/lib/content_object/forum/forum.class.php';
//require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
//require_once 'dokeos185_item_property.class.php';
//require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 forum_forum
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumForum extends Dokeos185CourseDataMigrationDataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'forum_forum';
        
    /**
     * Dokeos185ForumForum properties
     */
    const PROPERTY_FORUM_ID = 'forum_id';
    const PROPERTY_FORUM_TITLE = 'forum_title';
    const PROPERTY_FORUM_COMMENT = 'forum_comment';
    const PROPERTY_FORUM_THREADS = 'forum_threads';
    const PROPERTY_FORUM_POSTS = 'forum_posts';
    const PROPERTY_FORUM_LAST_POST = 'forum_last_post';
    const PROPERTY_FORUM_CATEGORY = 'forum_category';
    const PROPERTY_ALLOW_ANONYMOUS = 'allow_anonymous';
    const PROPERTY_ALLOW_EDIT = 'allow_edit';
    const PROPERTY_APPROVAL_DIRECT_POST = 'approval_direct_post';
    const PROPERTY_ALLOW_ATTACHMENTS = 'allow_attachments';
    const PROPERTY_ALLOW_NEW_THREADS = 'allow_new_threads';
    const PROPERTY_DEFAULT_VIEW = 'default_view';
    const PROPERTY_FORUM_OF_GROUP = 'forum_of_group';
    const PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE = 'forum_group_public_private';
    const PROPERTY_FORUM_ORDER = 'forum_order';
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_SESSION_ID = 'session_id';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_FORUM_ID, self :: PROPERTY_FORUM_TITLE, self :: PROPERTY_FORUM_COMMENT, self :: PROPERTY_FORUM_THREADS, self :: PROPERTY_FORUM_POSTS, self :: PROPERTY_FORUM_LAST_POST, self :: PROPERTY_FORUM_CATEGORY, self :: PROPERTY_ALLOW_ANONYMOUS, self :: PROPERTY_ALLOW_EDIT, self :: PROPERTY_APPROVAL_DIRECT_POST, self :: PROPERTY_ALLOW_ATTACHMENTS, self :: PROPERTY_ALLOW_NEW_THREADS, self :: PROPERTY_DEFAULT_VIEW, self :: PROPERTY_FORUM_OF_GROUP, self :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE, self :: PROPERTY_FORUM_ORDER, self :: PROPERTY_LOCKED, self :: PROPERTY_SESSION_ID);
    }

    /**
     * Returns the forum_id of this Dokeos185ForumForum.
     * @return the forum_id.
     */
    function get_forum_id()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_ID);
    }

    /**
     * Returns the forum_title of this Dokeos185ForumForum.
     * @return the forum_title.
     */
    function get_forum_title()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_TITLE);
    }

    /**
     * Returns the forum_comment of this Dokeos185ForumForum.
     * @return the forum_comment.
     */
    function get_forum_comment()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_COMMENT);
    }

    /**
     * Returns the forum_threads of this Dokeos185ForumForum.
     * @return the forum_threads.
     */
    function get_forum_threads()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_THREADS);
    }

    /**
     * Returns the forum_posts of this Dokeos185ForumForum.
     * @return the forum_posts.
     */
    function get_forum_posts()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_POSTS);
    }

    /**
     * Returns the forum_last_post of this Dokeos185ForumForum.
     * @return the forum_last_post.
     */
    function get_forum_last_post()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_LAST_POST);
    }

    /**
     * Returns the forum_category of this Dokeos185ForumForum.
     * @return the forum_category.
     */
    function get_forum_category()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_CATEGORY);
    }

    /**
     * Returns the allow_anonymous of this Dokeos185ForumForum.
     * @return the allow_anonymous.
     */
    function get_allow_anonymous()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_ANONYMOUS);
    }

    /**
     * Returns the allow_edit of this Dokeos185ForumForum.
     * @return the allow_edit.
     */
    function get_allow_edit()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_EDIT);
    }

    /**
     * Returns the approval_direct_post of this Dokeos185ForumForum.
     * @return the approval_direct_post.
     */
    function get_approval_direct_post()
    {
        return $this->get_default_property(self :: PROPERTY_APPROVAL_DIRECT_POST);
    }

    /**
     * Returns the allow_attachments of this Dokeos185ForumForum.
     * @return the allow_attachments.
     */
    function get_allow_attachments()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_ATTACHMENTS);
    }

    /**
     * Returns the allow_new_threads of this Dokeos185ForumForum.
     * @return the allow_new_threads.
     */
    function get_allow_new_threads()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_NEW_THREADS);
    }

    /**
     * Returns the default_view of this Dokeos185ForumForum.
     * @return the default_view.
     */
    function get_default_view()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_VIEW);
    }

    /**
     * Returns the forum_of_group of this Dokeos185ForumForum.
     * @return the forum_of_group.
     */
    function get_forum_of_group()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_OF_GROUP);
    }

    /**
     * Returns the forum_group_public_private of this Dokeos185ForumForum.
     * @return the forum_group_public_private.
     */
    function get_forum_group_public_private()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE);
    }

    /**
     * Returns the forum_order of this Dokeos185ForumForum.
     * @return the forum_order.
     */
    function get_forum_order()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_ORDER);
    }

    /**
     * Returns the locked of this Dokeos185ForumForum.
     * @return the locked.
     */
    function get_locked()
    {
        return $this->get_default_property(self :: PROPERTY_LOCKED);
    }

    /**
     * Returns the session_id of this Dokeos185ForumForum.
     * @return the session_id.
     */
    function get_session_id()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_ID);
    }

    /**
     * Check if the forum is valid
     * @param array $array the parameters for the validation
     * @return true if the forum is valid 
     */
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'forum', $this->get_forum_id()));
        
        if (! $this->get_forum_id() || ! ($this->get_forum_title() || $this->get_forum_comment()) || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $this->create_failed_element($this->get_forum_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'forum_forum', 'ID' => $this->get_forum_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new forum
     * @param array $array the parameters for the conversion
     * @return the new forum
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
        $chamilo_forum = new Forum();
        
        $category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Forum'));
        $chamilo_forum->set_parent_id($category_id);

        
        if (! $this->get_forum_title())
        {
            $chamilo_forum->set_title(substr($this->get_forum_comment(), 0, 20));
        }
        else
        {
            $chamilo_forum->set_title($this->get_forum_title());
        }
        
        if (! $this->get_forum_comment())
        {
            $chamilo_forum->set_description($this->get_forum_title());
        }
        else
        {
            $chamilo_forum->set_description($this->get_forum_comment());
        }
        
        $chamilo_forum->set_owner_id($new_user_id);
        $chamilo_forum->set_creation_date(strtotime($this->item_property->get_insert_date()));
        $chamilo_forum->set_modification_date(strtotime($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
        {
            $chamilo_forum->set_state(1);
        }
        
        $chamilo_forum->set_locked($this->get_locked());
            
        $chamilo_forum->create_all();
        
        $category = $this->get_id_reference($this->get_forum_category(), $this->get_database_name() . '.forum_category');
        $this->create_publication($chamilo_forum, $new_course_code, $new_user_id, 'forum', $category);
        
        //Add id references to temp table
        $this->create_id_reference($this->get_forum_id(), $chamilo_forum->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'forum', 'OLD_ID' => $this->get_forum_id(), 'NEW_ID' => $chamilo_forum->get_id())));
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