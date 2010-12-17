<?php
/*
 * Class to control a password field
 */
class VPassword extends Rule
{
	public function VPassword($field)
	{
		parent::Rule('This field is required');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if (VAL.length >'. $length .'&& VAL) return true; else return false;",
                    message: "Please enter a valid Password"
                });';
	}	
}
?>