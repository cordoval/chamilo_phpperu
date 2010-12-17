<?php
/*
 * Class to make it that at least one radiobutton has to be selected
 */
class Radio extends Rule
{
	public function Radio($field)
	{
		parent::Rule('This field is required');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if (isChecked(SelfID)) return true; else return false;",
                    message: "Please select a radio button"
                });';
	}
	
	public function control($value)
	{		
		$trim = trim($value);
		return (bool) !empty($trim);
	}
}
?>