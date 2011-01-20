<?php
/*
 * Class to check the given field to another password field
 */
class VConfirmpassword extends Rule
{
	public function VConfirmpassword($field)
	{
		parent::Rule();
		$this->script .= 'jQuery("#'.$this->field->get_name().'").validate({expression: "if ($.trim(VAL)) return true; else return false;", message: "Please enter the required field"}); ';
	}	
}
?>