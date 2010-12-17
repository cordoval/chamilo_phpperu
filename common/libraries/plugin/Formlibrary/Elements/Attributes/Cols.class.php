<?php
class Cols extends Attribute
{
	public function Cols($cols)
	{
		parent::Attribute($cols);
	}
	
	public function get_attribute()
	{
		$text = 'cols="'. $this->attribute .'"';		
		return $text;
	}
}
?>