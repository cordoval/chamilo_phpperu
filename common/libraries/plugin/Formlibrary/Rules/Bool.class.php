<?php
/*
 * Class to add a server side rule to an element, so that the input should be a boolean value only
 */
class Bool extends Rule
{
	/* Constructor	 * 
	 */
	public function Bool()
	{
		parent::Rule('Only boolean values are allowed');
	}
	
	/*
	 * Function that checks the input on the server side
	 */
	public function control($value)
	{
		if(preg_match('/^true$|^1|^false|^0$/i', $value))
		{
			$value = true;
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>