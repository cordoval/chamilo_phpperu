<?php
class Rows extends Attribute
{
	public function Rows($rows)
	{
		parent::Attribute($rows);
	}
	
	public function get_attribute()
	{
		$text = 'rows="'. $this->attribute .'"';		
		return $text;
	}
}
?>