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
	
	function create_publication($object, $course, $user, $tool)
	{
		//publication
        if ($this->item_property->get_visibility() <= 1)
        {
            $publication = new ContentObjectPublication();
            
            $publication->set_content_object($object->get_id());
            $publication->set_course_id($course);
            $publication->set_publisher_id($user);
            $publication->set_tool($tool);
            $publication->set_category_id(0);
            //$publication->set_from_date($mgdm->make_unix_time($this->item_property->get_start_visible()));
            //$publication->set_to_date($mgdm->make_unix_time($this->item_property->get_end_visible()));
            $publication->set_from_date(0);
            $publication->set_to_date(0);
            $publication->set_publication_date(strtotime($this->item_property->get_insert_date()));
            $publication->set_modified_date(strtotime($this->item_property->get_lastedit_date()));
            //$publication->set_modified_date(0);
            //$publication->set_display_order_index($this->get_display_order());
            $publication->set_display_order_index(0);
            
            if ($this->get_email_sent())
            {
                $publication->set_email_sent($this->get_email_sent());
            }
            else
            {
                $publication->set_email_sent(0);
            }
            
            $publication->set_hidden($this->item_property->get_visibility() == 1 ? 0 : 1);
            
            //create publication in database
            $publication->create();
        }
	}
	
	/**
	 * Sets the database name of this data class which is offcourse the database name of the course
	 */
	function get_database_name()
	{
		return $this->course->get_db_name();
	}
}

?>