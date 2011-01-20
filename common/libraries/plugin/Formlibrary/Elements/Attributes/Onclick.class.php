<?php
class Onclick extends Attribute
{
	public function Onclick($click)
	{
		parent::Attribute($click);
	}
	
	public function get_attribute()
	{
		$text = 'onclick="'. $this->attribute .'"';		
		return $text;
	}
}
?>