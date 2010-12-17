<?php
/*
 * Class to add a cliënt side and server side rule to an element, so that the input should be letters only
 */
class Alpha extends Rule
{
	/* Constructor
	 * It gets an element as parameter and adds the cliënt side validation function to the form
	 */
	public function Alpha($field)
	{
		parent::Rule('Only letters are allowed');	
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    expression: "if ((VAL.match(/^[a-z]+$/i) && VAL)) return true; else return false;",
                    message: "Please enter letters only"
                }); ';	
	}
	
	/*
	 * Function that checks the input on the server side
	 */
	public function control($value)
	{
		return (bool)preg_match('/^[a-z]+$/i', $value);
	}
}
?>