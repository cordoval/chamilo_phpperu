<?php
require_once Path:: get_plugin_path() . 'FormLibrary/Rules/Rule.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/ElementStorage/ElementStorage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Element.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Containers/Container.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Renderer/Render.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/form_library_html_editor.class.php';
/*
 * Main class to create forms
 */

class FormLibrary
{
	private $name;
	private $method;
	private $action;
	private $elementStorage;
	private $renderer;	

	/*
 	* Constructor for a form
 	*/
	function FormLibrary($name = null, $method = null, $action = null)
	{
		//Set name of the form if the user gave one		
		if(!empty($name))
		{
			$this->name = $name;
		}
		else
		//Set a unique name for the form
		{
			$a = "FormLibrary_" . mt_rand();			
		}
		
		//Set method of the form if the user gave one		
		if(!empty($method))
		{
			$this->method = $method;
		}
		else
		//Set a unique name for the form
		{
			$this->method = "POST";			
		}
		
		
		//Set the action of the form if the user submitted one
		if( !empty($action) )
		{
			$this->action = $action;
		}
		else
		{
			$this->action = $_SERVER['PHP_SELF'];
			if(!empty($_SERVER['QUERY_STRING']) )
			{
				$this->action .= '?'.$_SERVER['QUERY_STRING'];
			}
		}		
		
		//Make an ElementStorage object
		$this->elementStorage = new ElementStorage();
		
		$this->renderer = new DefaultRenderer($this);
	}
	
	/*
	 * Add element to the form
	 */
	function add_element($element)
	{
		$this->elementStorage->add_element($element);		
	}
	
	/*
	 * Retrieve element from the form
	 */
	function retrieve_element($element)
	{
		return $this->elementStorage->retrieve_element($element); 
	}
	
	/*
	 * Delete element from the form
	 */
	function delete_element($element)
	{
		$this->elementStorage->delete_element($element);
	}
	
	/*
	 * Get the element storage of the form
	 */
	function get_element_storage()
	{
		return $this->elementStorage;
	}	
	
	/*
	 * Check if the rules of every element are respected
	 * Server side validation
	 */
	function is_valid()
	{
		$postback = true;		
		if(empty($_POST))
		{	
				$postback = false;
				$valid = false;
		}
		
		$elements = $this->get_element_storage();	
			
		if($postback)
		{			
			$valid = true;		
			foreach ($elements->get_elements() as $element) 
			{
				if(!$element->is_valid())
					$valid = false; 				
			}			
			return $valid;
		}		
	}
	
	/*
	 * This function returns the HTML of the form and his elements
	 */
	function render()
	{
		$render = $this->renderer->render();		
		return $render;							
	}
	
	/*
	 * Via this function you can set a specific renderer to the form
	 */
	function set_renderer($render)
	{
		$this->renderer = $render;
	}	

	/*
	 * This function returns the name of the form
	 */
	function get_name()
	{
		return $this->name;
	}
	
	/*
	 * This function returns the method of the form
	 */
	function get_method()
	{
		return $this->method;
	}
	
	/*
	 * This function returns the action of the form
	 */
	function get_action()
	{
		return $this->action;
	}
	
	/*
	 * This function set the action of the form
	 */
	function set_action($action)
	{
		if(!is_null($action))
		{
			$this->action = $action;
		}
	}

	/*
	 * This functions returns the input error messages
	 */	
	function get_error()
	{
		return $this->error;
	}
	
	/*
	 * Collect all the javascript code per element
	 */
	function get_javascripts()
	{
		$code = '';
		$elements = $this->elementstorage->get_elements();
		foreach($elements as $element)
		{
			$code .= $element->get_javascript();
		}
		return $code;
	}	
}
?>