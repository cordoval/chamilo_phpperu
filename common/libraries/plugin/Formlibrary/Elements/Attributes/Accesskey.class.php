<?php
class Accesskey extends Attribute
{
	public function Accesskey($key)
	{
		parent::Attribute($key);
	}
	
	public function get_attribute()
	{
		$text = 'accesskey="'. $this->attribute .'"';		
		return $text;
	}
}
?>