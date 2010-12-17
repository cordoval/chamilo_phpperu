<?php
/*
 * Class to make it that there has to be one value selected
 */
class VSelect extends Rule
{
	public function VSelect($field)
	{
		parent::Rule('This field is required');
		$this->script .= 'jQuery("#'.$this->field->get_name().'").validate({expression: "if ($.trim(VAL)) return true; else return false;", message: "Please enter the required field"}); ';
	}
	
	public function control($value)
	{		
		$trim = trim($value);
		return (bool) !empty($trim);
	}
}
?>