<?php
/**
 * 
 */
abstract class GradeBookConnector
{
	static function factory($application)
	{
		require_once Path :: get_application_path() . '/lib/' . strtolower($application).  '/connector/'. strtolower($application).'_gradebook_connector.class.php';
		$class_name = $application . '_gradebook_connector';
        $class = Utilities :: underscores_to_camelcase($class_name);
        return new $class();
	}
	
	abstract function get_tracker_score($application, $publication_id);
	
	abstract function get_tracker_user($application, $publication_id);
}
?>