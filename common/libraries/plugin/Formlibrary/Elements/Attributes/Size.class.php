<?php
class Size extends Attribute
{
	public function Size($size)
	{
		parent::Attribute($size);
	}
	
	public function get_attribute()
	{
		$text = 'size="'. $this->attribute .'"';		
		return $text;
	}
}
?>