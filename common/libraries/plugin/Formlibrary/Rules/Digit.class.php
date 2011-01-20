<?php
/*
 * Class to add a cliënt side and server side rule to an element, so that the input should be digits only
 */
class Digit extends Rule
{
	/* Constructor
	 * It gets an element as parameter and adds the cliënt side validation function to the form
	 */
	public function Digit($field)
	{
		parent::Rule('Only digits are allowed');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
                    message: "Please enter a valid number"
                }); ';					
	}
	/*
	 * Function that checks the input on the server side
	 * 0-9
	 */
	public function control($value)
	{
		return (bool) preg_match('/^[0-9]+$/', $value);
	}	
}
?>