<?php
/*
 * Class to check if the uploaded file is a valid file
 */
class File extends Rule
{
	public function File($type)
	{
		parent::Rule('Only valid files are allowed');				
	}
	
	public function control($value)
	{
		return preg_match('{^[^\\/\*\?\:\,]+$}', $value);
	}
}
?>