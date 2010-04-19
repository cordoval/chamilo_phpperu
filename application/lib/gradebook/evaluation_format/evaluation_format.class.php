<?php
abstract class EvaluationFormat
{
    //returns how the evaluation should be shown on screen
    

	function factory($folder, $type)
	{
		require_once dirname(__FILE__) . '/' . strtolower($folder).  '/'. $type;
		$index = strpos($type, '.');
		$class_name = substr($type, 0, $index);
        $class = Utilities :: underscores_to_camelcase($class_name);
        return new $class();
	}
	
	static function get_folder($type)
	{
		$folder_inventory = array(
		'Letters' => 'letters',
		'Points on twenty' => 'points',
		'Points on thirty' => 'points',
		'Points on fifty' => 'points',
		'Points on hundred' => 'points',
		);
		return $folder_inventory[$type];
	}
	
	static function name_to_underscore($name)
	{
		return str_replace(' ', '_', $name);
	}
	
	abstract function get_evaluation_field_type();
	
	abstract function get_evaluation_field_name();
	
	abstract function get_evaluation_format_name();
	
	abstract function get_default_active_value();
	
	abstract function get_score_set();
}
?>