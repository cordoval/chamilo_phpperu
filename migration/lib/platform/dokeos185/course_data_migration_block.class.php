<?php

require_once dirname(__FILE__) . '/data_class/dokeos185_course.class.php';

/**
 * abstract class to migrate course data (for example the announcements of all courses)
 * @author vanpouckesven
 * 
 * //TODO: migrate deleted files in retrieve objects
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
	
	/**
	 * For each dataclass the migration status over all the courses will be stored here
	 * For example $migration_status['Dokeos185Announcement']['failed'] = 0; 
	 * The following statusses / counts will be included: migrated, failed
	 * @var String[]
	 */
	private $migration_status;
	
	protected function migrate_data()
	{
		$dm = Dokeos185DataManager :: get_instance();
		$retrieve_course = new Dokeos185Course();
		
		$total_count = $dm->count_all_objects($retrieve_course);
		$courses_migrated = 0;
			
		while ($courses_migrated < $total_count)
		{
	        $offset = $courses_migrated;
	        $limit = self :: MAX_RETRIEVE_LIMIT;
	            
	        $courses = $dm->retrieve_all_objects($retrieve_course, $offset, $limit);
	        while($course = $courses->next_result())
	        {
	          	$this->course = $course;
	          	
	          	$this->get_file_logger()->log_message(Translation :: get('StartMigrationCourse', array('COURSE' => $course->get_code())));
	          	parent :: migrate_data();
	          	$this->get_file_logger()->log_message(Translation :: get('FinishMigrationCourse', array('COURSE' => $course->get_code())) . "<br />\n");
	          	
	           	$courses_migrated++;
	        }
        }
	}
	
	/**
	 * Finish te migration process
	 * Change the block registration
	 * Logfiles & Messages
	 */
	protected function finish_migration()
	{
		$this->log_data_class_statusses();
		parent :: finish_migration();
	}
	
	private function log_data_class_statusses()
	{
		foreach($this->get_data_classes() as $data_class)
		{
			$this->log_message_and_file(Translation :: get('MigrationResultsForTable', array('TABLE' => $data_class->get_table_name())));
			
			$migrated_objects = $this->migration_status[$data_class->get_class_name()]['migrated'];
			$failed_objects = $this->migration_status[$data_class->get_class_name()]['failed'];
			
			if($migrated_objects == 1)
			{
				$message = 'ObjectMigrated';
			}
			else
			{
				$message = 'ObjectsMigrated';
			}
			
			$this->log_message_and_file(Translation :: get($message, array('OBJECTCOUNT' => $migrated_objects)));
			
			if($failed_objects == 1)
			{
				$message = 'ObjectNotMigrated';
			}
			else
			{
				$message = 'ObjectsNotMigrated';
			}
		}
	}
	
	/**
	 * Logs messages before starting the migration of each dataclass
	 * @param MigrationDataClass $data_class
	 */
	protected function pre_data_class_migration_messages_log(MigrationDataClass $data_class)
	{
		$this->get_file_logger()->log_message(Translation :: get('StartMigrationForTable', array('TABLE' => $data_class->get_table_name())));
	}
	
	/**
	 * Logs the messages after the migration of each dataclass
	 * @param int $migrated_objects
	 * @param int $failed_objects
	 * @param MigrationDataClass $data_class
	 */
	protected function post_data_class_migration_messages_log($migrated_objects, $failed_objects, MigrationDataClass $data_class)
	{
		$this->migration_status[$data_class->get_class_name()]['migrated'] += $migrated_objects;
		$this->migration_status[$data_class->get_class_name()]['failed'] += $failed_objects;
		
		if($migrated_objects == 1)
		{
			$message = 'ObjectMigrated';
		}
		else
		{
			$message = 'ObjectsMigrated';
		}
		
		$this->get_file_logger()->log_message(Translation :: get($message, array('OBJECTCOUNT' => $migrated_objects)));
		
		if($failed_objects == 1)
		{
			$message = 'ObjectNotMigrated';
		}
		else
		{
			$message = 'ObjectsNotMigrated';
		}
		
        $this->get_file_logger()->log_message(Translation :: get($message, array('OBJECTCOUNT' => $failed_objects)));
        $this->get_file_logger()->log_message(Translation :: get('FinishedMigrationForTable', array('TABLE' => $data_class->get_table_name())) . "<br />\n");
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
			$data_class->set_course($this->course);
		}
		
		return $this->data_classes;
	}
	
	abstract function get_course_data_classes();
}