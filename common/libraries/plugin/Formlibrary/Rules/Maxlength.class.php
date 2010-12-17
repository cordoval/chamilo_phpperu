<?php
/*
 * Class to check is the length inserted value is repected to the given value
 */
class Maxlength extends Rule
{
	protected $length;
	
	public function Maxlength($field, $length)
	{
		parent::Rule('The maximum allowed number of characters is '.$length );
		$this->length = $length;
		$this->script .= ' jQuery("#'.$field->get_name().'").validate({
                    expression: "if (VAL.length <='. $length .') return true; else return false;",
                    message: "The maximum allowed characters is ' .  $length . '" 
                }); ';
	}
	
	public function control($value)
	{
		if(strlen($value)<=$this->length)
			return true;
		else return false;
	}
}
?>