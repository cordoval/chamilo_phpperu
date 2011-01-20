<?php
/*
 * Class to create a hyperlink
 */

class Link extends Element
{
	/*
	 * Constructor for the Text class
	 */
	public function Link($name, $link)
	{
		//call constructor of Element class
		parent::Element($name, $link);				
	}
	
	/*
	 * Returns the HTML of the created textfield
	 */
	public function render()
	{		
		$html = array();
		$html[] = sprintf('<a href="' . $this->value. '" >'. $this->name .'</a>');			
		return implode('\n', $html);	
	}
}
?>