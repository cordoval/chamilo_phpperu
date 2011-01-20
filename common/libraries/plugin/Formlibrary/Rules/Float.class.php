<?php
/*
 * Class to check is the inserted value a a float value
 */
class Float extends Rule
{
	public function Float($field)
	{
		parent::Rule('Only float values are allowed');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if (VAL.match(/^-?([0-9]*\.?,?[0-9]+)$/) && VAL) return true; else return false;",
                    message: "Please enter a valid float value"
                }); ';
	}
	
	public function control($value)
	{
		return (bool) preg_match('/^-?([0-9]*\.?,?[0-9]+)$/', $value);
	}
}
?>