<?php
/*
 * Class to create an image, it inherits from Element class
 */

class Image extends Element
{
	/*
	 * Constructor for the Image class
	 */
	public function Image($source)
	{
		//call constructor of Element class
		parent::Element($source);		
	}
	
	/*
	 * Returns the HTML of the created Image
	 */
	public function render()
	{
		$html = array();
		$html[] = sprintf(		
			'<img src="%s" %s" >',
			$this->name,
			$this->attributestorage->get_attributes());
		return implode('\n', $html);			
	}
}
?>