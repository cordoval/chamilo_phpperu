<?php
class Style extends Attribute
{
	public function Style($style)
	{
		parent::Attribute($style);
	}
	
	public function get_attribute()
	{
		$text = '';
		$text = 'style="'. $this->attribute .'"';
		return $text;
	}
}
?>