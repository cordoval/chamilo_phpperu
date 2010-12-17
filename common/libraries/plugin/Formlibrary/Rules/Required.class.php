<?php
/*
 * Class to make a field required
 */
class Required extends Rule
{
	public function Required($field)
	{
		parent::Rule('This field is required');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({expression: "if ($.trim(VAL)) return true; else return false;", message: "Please enter the required field"}); ';
	}
	
	public function control($value)
	{		
		$trim = trim($value);
		return (bool) !empty($trim);
	}
}
?>