<?php

/*
 *	Class AttributeStorage, it is meant to hold attributes of a certain element
 *	An instance of this class is a datamember of a created element 
 */
require_once Path:: get_plugin_path() . 'FormLibrary/Elements/Attributes/Attribute.class.php';
class AttributeStorage
{
	protected $attributes; 	//array of all the attributes of an element

	/*
	 * Constructor to create an instance of the AttributeStorage class
	 */
	public function AttributeStorage()
	{
		$this->attributes = array();
	}
	
	/*
	 * Function to add an attribute to an element
	 */
	public function add_attribute($attribute)
	{
		if(!empty($attribute))
		{
			array_push($this->attributes, $attribute);			
		}	
	}
	
	/*
	 * This function returns the array of attributes of an element
	 */
	public function get_attributes()
	{
		$attributes = '';
		foreach($this->attributes as $val)		
		{			
			$attributes .= ' ' . $val->get_attribute();
		}
		return $attributes;
	}
}