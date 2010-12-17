<?php
/*
 * Class to add a cliënt side and server side rule to an element, so that the input should be letters and numbers only
 */
class AlphaNum extends Rule
{
	/* Constructor
	 * It gets an element as parameter and adds the cliënt side validation function to the form
	 */
	public function AlphaNum($field)
	{
		parent::Rule('Only letters and numbers are allowed');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if ((VAL.match(/^[a-z0-9]+$/i) && VAL)) return true; else return false;",
                    message: "Please enter letters and numbers only"
                }); ';
	}
	
	/*
	 * Function that checks the input on the server side
	 */
	public function control($value)
	{
		return (bool)preg_match('/^[a-z0-9]+$/i', $value);
	}
}
?>