<?php
class Alt extends Attribute
{
	public function Alt($alt)
	{
		parent::Attribute($alt);
	}
	
	public function get_attribute()
	{
		$text = '';
		$text = 'alt="'. $this->attribute .'"';
		return $text;
	}
}
?>