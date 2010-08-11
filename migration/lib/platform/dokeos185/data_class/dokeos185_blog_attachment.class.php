<?php
/**
 * $Id: dokeos185_blog_attachment.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 blog_attachment
 *
 * @author Sven Vanpoucke
 */
class Dokeos185BlogAttachment extends Dokeos185CourseDataMigrationDataClass
{
	const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'blog_attachment';
        
    /**
     * Dokeos185BlogAttachment properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_PATH = 'path';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_POST_ID = 'post_id';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_BLOG_ID = 'blog_id';
    const PROPERTY_COMMENT_ID = 'comment_id';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_PATH, self :: PROPERTY_COMMENT, self :: PROPERTY_SIZE, self :: PROPERTY_POST_ID, self :: PROPERTY_FILENAME, self :: PROPERTY_BLOG_ID,
        			 self :: PROPERTY_COMMENT_ID);
    }

    /**
     * Returns the id of this Dokeos185BlogAttachment.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the path of this Dokeos185BlogAttachment.
     * @return the path.
     */
    function get_path()
    {
        return $this->get_default_property(self :: PROPERTY_PATH);
    }

    /**
     * Returns the comment of this Dokeos185BlogAttachment.
     * @return the comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the size of this Dokeos185BlogAttachment.
     * @return the size.
     */
    function get_size()
    {
        return $this->get_default_property(self :: PROPERTY_SIZE);
    }

    /**
     * Returns the post_id of this Dokeos185BlogAttachment.
     * @return the post_id.
     */
    function get_post_id()
    {
        return $this->get_default_property(self :: PROPERTY_POST_ID);
    }

    /**
     * Returns the filename of this Dokeos185BlogAttachment.
     * @return the filename.
     */
    function get_filename()
    {
        return $this->get_default_property(self :: PROPERTY_FILENAME);
    }
    
	/**
     * Returns the blog_id of this Dokeos185BlogAttachment.
     * @return the blog_id.
     */
    function get_blog_id()
    {
        return $this->get_default_property(self :: PROPERTY_BLOG_ID);
    }
    
	/**
     * Returns the comment_id of this Dokeos185BlogAttachment.
     * @return the comment_id.
     */
    function get_comment_id()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT_ID);
    }

    /**
     * Check if the forum attachment is valid
     * @return true if the forum is valid 
     */
    function is_valid()
    {
    	$course = $this->get_course();
    	$path = $this->get_data_manager()->get_sys_path() . '/courses/' . $course->get_directory() . '/upload/blog/' . $this->get_path();
    	
    	$post_id = $this->get_id_reference($this->get_post_id(), $this->get_database_name() . '.blog_post');
    	$blog_id = $this->get_id_reference($this->get_blog_id(), $this->get_database_name() . '.blog');
    	$comment_id = $this->get_id_reference($this->get_comment_id(), $this->get_database_name() . '.blog_comment');
    	
    	if ( !$post_id || !$blog_id || !$this->get_filename() || ! $this->get_path() || !file_exists($path) || ($this->get_comment_id() > 0 && !$comment_id))
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'blog_attachment', 'ID' => $this->get_id())));
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
        $course = $this->get_course();
    	$path = $this->get_data_manager()->get_sys_path() . '/courses/' . $course->get_directory() . '/upload/blog/';
    	
    	if(!$this->get_comment_id())
    	{
    		$object_id = $this->get_id_reference($this->get_post_id(), $this->get_database_name() . '.blog_post');
    	}
    	else
    	{
    		$object_id = $this->get_id_reference($this->get_comment_id(), $this->get_database_name() . '.blog_comment');
    	}
    	
    	$object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
    	
    	$hash = md5($this->get_filename());
    	$new_path = Path :: get(SYS_REPO_PATH) . $object->get_owner_id() . '/' . Text :: char_at($hash, 0) . '/';
    	$file_exists = file_exists($new_path . $hash);
    	
    	$migrated_hash = $this->migrate_file($path, $new_path, $this->get_path(), $hash);
    	
    	if($file_exists && $hash == $migrated_hash)
    	{
    		$document = RepositoryDataManager :: retrieve_document_from_hash($object->get_owner_id(), $migrated_hash);
    	}
    	
    	if(!$document)
    	{
    		$document = new Document();
            $document->set_filename($this->get_filename());
            $document->set_path($object->get_owner_id() . '/' . Text :: char_at($migrated_hash, 0) . '/' . $migrated_hash);
            $document->set_filesize($this->get_size());
            $document->set_hash($migrated_hash);
            $document->set_title($this->get_filename());
            $document->set_description($this->get_filename());
            $document->set_owner_id($object->get_owner_id());
            $document->set_creation_date($object->get_creation_date());
            $document->set_modification_date($object->get_modification_date());

            $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($object->get_owner_id(), Translation :: get('Documents'));
            $document->set_parent_id($chamilo_category_id);

            $document->create();
    	}
    	
    	$object->attach_content_object($document->get_id());
    	
    	//Add id references to temp table
        $this->create_id_reference($this->get_id(), $document->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'forum_attachment', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $document->get_id())));
    	
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