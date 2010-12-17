<?php
/*
 * Class for a container on a form
 */
require_once Path:: get_plugin_path() . 'FormLibrary/Containers/Fieldset.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Containers/Grouping.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/ElementFinder.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/ImageSelecter.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Timepicker.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/CustomElements/Datepicker.class.php';


/*
 * Abstract class Container
 * Is had the same functions as an element storage to store his elements
 */
abstract class Container 
{
	private $name;		//The name of the container
	private $label;		//The label of the container
	protected $elements;	//Array: The elements that are in the container
	protected $elementerror;	//Array: The errors that occur when the value of a field isn't correct	
	private $rulestorage;		
	
	/*
	 * Constructor 
	 */
	public function Container($name, $label = null)
	{
		$this->name = $name;
		$this->label = $label;
		$this->elements = array();
		$this->rulestorage = new RuleStorage();	
	}
	
	/*
	 * Add an element to the group
	 */
	public function add_element($element)
	{
		if(!is_null($element))
		{
			array_push($this->elements, $element);
		}
	}
	
	/*
	 *	Remove an element from the container 
	 */
	public function delete_element($element)
	{
		if(!is_null($element))
		{
			for($i=0; $i<count($this->elements);$i++)
			{
				if($this->elements[$i]===$element)
    				unset($this->elements[$i]);
			}	
		}	
	}
	
	/*
	 * Retrieve an element that's in the container
	 */
	public function retrieve_element($element)
	{
		$object = null;
		if(!is_null($element))
		{
			foreach ($this->elements as $value) 
			{
    			if($value->get_name() == $element)
    				$object = $value;
			}		
		}		
		return $object;
	}
	
	/*
	 * Function that returns the name of the container
	 */
	public function get_name()
	{
		return $this->name;
	}
	
	/*
	 * Via this function you can set the name of the container
	 */
	public function set_name($name)
	{
		if(!empty($name))
		{
			$this->name = $name;
		}
	}
	
	/*
	 * This function returns the label of the container
	 */
	public function get_label()
	{
		return $this->label;
	}
	
	/*
	 * This function returns the elements in the container
	 */
	public function get_elements()
	{
		return $this->elements;
	}
	
	/*
	 * This function returns the error messages when a rule isn't respected
	 */
	public function get_errors()
	{
		return $this->elementerror;
	}

	/*
	 * This functions checks if the values of the elements in the container are valid
	 */
	public function is_valid()
	{
		$valid = true;
		foreach($this->elements as $element)
		{	
			foreach($element->get_rulestorage()->get_rules() as $rule)			
			{
				$valid = $rule->control($element->get_value());
				if(!$valid)
				{
					$this->elementerror .= $rule->get_message() . '<br/>';
					$valid = false;
				}
			}
		}
		return $valid;	
	}
	
	/*
	 * Getter for the rulestorage of the element
	 */
	public function get_rulestorage()
	{
		return $this->rulestorage;
	}		
}
?>