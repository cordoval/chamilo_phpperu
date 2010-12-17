<?php
/*
 * ElementStorage class
 * An instance of this class is a datamember of an element
 */
class ElementStorage
{
	protected $elements; //Array: It contains all the elements that are added to the form
	
	/*
	 * Constructor for an element storage
	 */
	public function ElementStorage()
	{
		$this->elements = array();	
	}
	
	/*
	 * Add an element to the elementstorage
	 */
	public function add_element($element)
	{
		if(!is_null($element))
		{
			array_push($this->elements, $element);
		}
	}
	
	/*
	 * Delete an element from the elementstorage
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
	 * Retrieve a certain element from the elementstorage
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
	 * Get the array of the elements that were addded to the form
	 */
	public function get_elements()
	{
		return $this->elements;
	}
}
?>