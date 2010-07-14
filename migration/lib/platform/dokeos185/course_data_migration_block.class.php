<?php

require_once dirname(__FILE__) . '/../data_class/dokeos185_course.class.php';

/**
 * abstract class to migrate course data (for example the announcements of all courses)
 * @author vanpouckesven
 *
 */
abstract class CourseDataMigrationBlock extends MigrationBlock
{
	/**
	 * We will store the dataclasses because we will call the function get_data_classes will get called for each course
	 * @var Dokeos185MigrationDataClass[] $data_classes
	 */
	private $data_classes;
	
	/**
	 * The current course, we need to give this to each data class
	 * @var Dokeos185Course $course
	 */
	private $course;
	
	function migrate_data()
	{
		//TODO: retrieve all courses
		parent :: migrate_data();
	}
	
	function convert_object($object)
	{
		$object->set_course($this->course);
		return parent :: convert_object($object);
	}
	
	function get_data_classes()
	{
		if(!$this->data_classes)
		{
			$this->data_classes = $this->get_course_data_classes();
		} 
		
		foreach($this->data_classes as $data_class)
		{
			$data_class->set_course($this->current_course);
		}
		
		return $this->data_classes;
	}
	
	abstract function get_course_data_classes();
}