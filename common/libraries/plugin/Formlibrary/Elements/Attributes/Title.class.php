<?php
class Title extends Attribute
{
	public function Title($title)
	{
		parent::Attribute($title);
	}
	
	public function get_attribute()
	{
		$text = 'title="'. $this->attribute .'"';		
		return $text;
	}
}
?>