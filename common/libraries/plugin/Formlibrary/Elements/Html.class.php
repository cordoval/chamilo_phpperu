<?php
class AddHtml extends Element
{
	protected $text;
	/*
	 * Constructor for the Text class
	 */
	public function AddHtml($text)
	{
		//call constructor of Element class
		parent::Element($text);
		$this->text = $text;	
	}
	
	/*
	 * Returns the HTML of the created textfield
	 */
	public function render()
	{
		return implode('', $this->text);	
	}
}
?>