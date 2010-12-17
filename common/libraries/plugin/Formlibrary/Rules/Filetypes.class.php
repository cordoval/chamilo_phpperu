<?php
/*
 * Rule to check the uploaded filetype
 */
class Filetypes extends Rule
{
	protected $types;
	
	public function Filetypes($field, $types)
	{
		parent::Rule('This filetype isn\'t allowed');		
		$this->types = $types;
	}		
	
	public function control($value)
	{		
		$valid = false;
		$filetype = substr($value, strrpos($value, '.') + 1);
		if(is_array($this->types))
		{
			foreach($this->types as $type)
			{
				if(strcmp($type, $filetype)==0)				
					$valid = true;				
			}
		}
		echo $valid;
		return $valid;
	}
}
?>