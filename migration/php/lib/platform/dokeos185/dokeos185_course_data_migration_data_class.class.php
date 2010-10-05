<?php
/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

require_once dirname(__FILE__) . '/dokeos185_migration_data_class.class.php';
require_once dirname(__FILE__) . '/data_class/dokeos185_item_property.class.php';

/**
 * Abstract class to be used by all the tables that are within a course database of dokeos 1.8.5
 * @author Sven Vanpoucke
 */
abstract class Dokeos185CourseDataMigrationDataClass extends Dokeos185MigrationDataClass
{
	/**
	 * The current course for this data
	 * @var Dokeos185Course
	 */
	private $course;

	/**
	 * Item property, used for most tools
	 * @var Dokeos185ItemProperty
	 */
	protected $item_property;
	
	/**
	 * Sets the course
	 * @param Dokeos185Course $course
	 */
	function set_course($course)
	{
		$this->course = $course;
	}
	
	/**
	 * Retrieves the course
         * @return Course
	 */
	function get_course()
	{
		return $this->course;
	}
        

	
	function get_item_property()
	{
		return $this->item_property;
	}
	
	function set_item_property($item_property)
	{
		$this->item_property = $item_property;
	}

    function get_email_sent()
    {
        return 0;
    }
    
    /**
     * Creates a publication from a content object
     * @param ContentObject $object
     * @param int $course_id
     * @param int $user_id
     * @param String $tool
     * @param int $category_id
     * @param array() $target_users
     * @param array() $target_groups
     */
	function create_publication($object, $course_id, $user_id, $tool, $category_id = 0, $target_users = null, $target_groups = null)
	{
		//publication
        $publication = new ContentObjectPublication();
            
        $publication->set_content_object($object);
        $publication->set_content_object_id($object->get_id());
        $publication->set_course_id($course_id);
        $publication->set_publisher_id($user_id);
        $publication->set_tool($tool);

        //target users, groups
        $publication->set_target_users($target_users);
        $publication->set_target_course_groups($target_groups);
          
        $publication->set_category_id($category_id);

        $publication->set_from_date(0);
        $publication->set_to_date(0);

        $publication->set_display_order_index(0);
            
        $publication->set_email_sent($this->get_email_sent());
            
        if($this->item_property)
        {
      		$publication->set_hidden($this->item_property->get_visibility() == 1 ? 0 : 1);
      		$publication->set_publication_date(strtotime($this->item_property->get_insert_date()));
        	$publication->set_modified_date(strtotime($this->item_property->get_lastedit_date()));
        }
        else
        {
        	$publication->set_hidden($object->get_state() - 1);
        	$publication->set_publication_date($object->get_creation_date());
        	$publication->set_modified_date($object->get_modification_date());
        }
            
        //create publication in database
        $publication->create();
        
		return $publication;        
	}
	
	/**
	 * Creates a complex content object item with given parametesr
	 * @param ContentObject $object
	 * @param int $parent_id
	 * @param int $user_id
	 * @param int $add_date
	 * @param array() $additional_properties
	 */
	function create_complex_content_object_item($object, $parent_id, $user_id, $add_date = null, $display_order = null, $additional_properties = array())
	{
		$complex_content_object_item = ComplexContentObjectItem :: factory($object->get_type());
        $complex_content_object_item->set_user_id($user_id);
        $complex_content_object_item->set_parent($parent_id);
        $complex_content_object_item->set_ref($object->get_id());
        $complex_content_object_item->set_add_date($add_date ? $add_date : time());
        $complex_content_object_item->set_display_order($display_order);
        $complex_content_object_item->set_additional_properties($additional_properties);
        $complex_content_object_item->create();
        
        return $complex_content_object_item;
	}
	
	/**
	 * Creates a feedback publication
	 * @param ContentObject $object
	 * @param int $publication_id
	 * @param int $complex_content_object_item_id
	 */
	function create_feedback($object, $publication_id, $complex_content_object_item_id, $creation_date = null, $modification_date = null)
	{
		$feedback = new FeedbackPublication();
		$feedback->set_application('weblcms');
		$feedback->set_fid($object->get_id());
		$feedback->set_pid($publication_id);
		$feedback->set_cid($complex_content_object_item_id);
		$feedback->set_creation_date($creation_date ? $creation_date : time());
		$feedback->set_modification_date($modification_date ? $modification_date : time());
		$feedback->create();
		
		return $feedback;
	}
	
	/**
	 * Gets the database name of this data class which is offcourse the database name of the course
	 */
	function get_database_name()
	{
		return $this->course->get_db_name();
	}
}

?>