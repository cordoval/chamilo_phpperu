<?php
/*
 * Class to have the rule that at least one checkbox has to be checked
 */
class VCheckbox extends Rule
{
	public function VCheckbox($field)
	{
		parent::Rule('This field is required');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if (isChecked(SelfID)) return true; else return false;",
                    message: "Please check atleast one checkbox"
                });';
	}
	
	public function control($value)
	{		
		$trim = trim($value);
		return (bool) !empty($trim);
	}
}
?>