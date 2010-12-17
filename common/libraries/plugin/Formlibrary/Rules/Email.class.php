<?php
/*
 * Class to add a cliënt side and server side rule to an element, so that the input should be a valid email address
 */
class Email extends Rule
{
	/* Constructor
	 * It gets an element as parameter and adds the cliënt side validation function to the form
	 */
	public function Email($field)
	{
		parent::Rule('Please provide a valid e-mailaddress');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
                    	expression: "if (VAL.match(/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i)) return true; else return false;",
                    message: "Please enter a valid Email ID"
                });';
	}
	
	/*
	 * Function that checks the input on the server side
	 */
	public function control($value)
	{
		return preg_match('/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i', $value);
	}
}
?>