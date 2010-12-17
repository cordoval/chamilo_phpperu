<?php
class Tabindex extends Attribute
{
	public function Tabindex($index)
	{
		parent::Attribute($index);
	}
	
	public function get_attribute()
	{
		$text = 'tabindex="'. $this->attribute .'"';		
		return $text;
	}
}
?>