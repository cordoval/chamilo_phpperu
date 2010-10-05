<?php
/**
 * 
 */
abstract class GradeBookConnector
{
	static function factory($application, $tool = null)
	{
		require_once Path :: get_application_path() . '/lib/' . strtolower($application).  '/connector/'. strtolower($application).'_gradebook_connector.class.php';
		$class_name = $application . '_gradebook_connector';
        $class = Utilities :: underscores_to_camelcase($class_name);
        return new $class($tool);
	}
	
	abstract function get_tracker_score($publication_id);
	
	abstract function get_tracker_user($publication_id);
	
	abstract function get_tracker_date($publication_id);
}
?>