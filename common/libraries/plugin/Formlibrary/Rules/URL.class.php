<?php
/*
 * Class to check is the inserted value is a valid URL
 */
class URL extends Rule
{
	public function URL($field)
	{
		parent::Rule('Please provide a valid URL');
		$this->script .= 'jQuery("#'.$field->get_name().'").validate({
					expression: "if (VAL.match(/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/) && VAL) return true; else return false;",
                    message: "Please enter a valid url"                    
                });';
	}
	
	public function control($value)
	{
		$regex = '/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/';
		$result = preg_match( $regex, $value, $match );

		return $result;
	}
}
?>