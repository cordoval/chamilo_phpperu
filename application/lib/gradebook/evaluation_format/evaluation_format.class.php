<?php
abstract class EvaluationFormat
{
    //returns how the evaluation should be shown on screen
    private $score;

	function factory($type, $install, $folder = null)
	{
		if ($install)
		{
			require_once dirname(__FILE__) . '/' . $folder .  '/'. $type;
			$index = strpos($type, '.');
			$class_name = substr($type, 0, $index);
	        $class = Utilities :: underscores_to_camelcase($class_name);
		}
		else
		{
            $name = self :: name_to_underscore($type);
            require_once dirname(__FILE__) . '/' . self :: get_folder($type) .  '/'. $name . '.class.php';
	        $class = Utilities :: underscores_to_camelcase($name);
		}
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
		'Percentage' => 'points',
		);
		return $folder_inventory[$type];
	}
	
	static function name_to_underscore($name)
	{
		return Utilities :: camelcase_to_underscores(str_replace(' ', '_', $name));
	}
	
	function set_score($score)
	{
		$this->score = $score;
	}
	
	function get_score()
	{
		return $this->score;
	}
	
	abstract function get_evaluation_field_type();
	
	abstract function get_evaluation_field_name();
	
	abstract function get_evaluation_format_name();
	
	abstract function get_default_active_value();
	
	abstract function get_score_set();
	
	abstract function get_formatted_score();
}
?>