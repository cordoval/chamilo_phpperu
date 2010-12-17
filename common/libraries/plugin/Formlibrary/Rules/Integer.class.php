<?php
/*
 * Class to check is the inserted value is an integer
 */
class Integer extends Rule
{
	public function Integer($field)
	{
		parent::Rule('Only integers are allowed');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
                    message: "Please enter a valid integer"
                }); ';
	}
	
	public function control($value)
	{
		return (bool) preg_match("/^-?[0-9]+$/", $value);
	}
}
?>