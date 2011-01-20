<?php
require_once Path:: get_plugin_path() . 'FormLibrary/FormLibrary.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Renderer/DefaultRenderer.class.php';
/*
 * Class to render the elements that were added to the form
 */

abstract class Render
{
	protected $form;	//The created form
	
	/*
	 * Constructor for the Renderer class. 
	 */
	public function Render($form)
	{
		$this->form = $form;
	}	
}
?>