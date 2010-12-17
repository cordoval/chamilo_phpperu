<?php
class Source extends Attribute
{
	public function Source($url)
	{
		parent::Attribute($url);
	}
	
	public function get_attribute()
	{
		$text = 'src="'. $this->attribute .'"';		
		return $text;
	}
}
?>