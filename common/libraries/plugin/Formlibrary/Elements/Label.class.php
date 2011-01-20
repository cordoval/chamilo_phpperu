<?php
/*
 * Class to create a label
 */
class Label extends Element
{
	protected $name; //string: the displayed text for the label
		
	/*
	 * Constructor for the Label class
	 */
	public function Label($name)
	{
		parent::Element($name);		
	}
	
	/*
	 * This function returns the HTML of the created label
	 */
	public function render()
	{
		$html = array();
		$html[] = sprintf('<label>' . '%s' .'</label>', $this->name);
		return implode('\n', $html);
	}
}
?>