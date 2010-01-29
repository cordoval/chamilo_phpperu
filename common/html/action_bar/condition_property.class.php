<?php

/**
 * Class that represents a condition property
 * Used in action bar renderer to translate these condition property's to actual PatternMatchConditions
 * @author Sven Vanpoucke
 *
 */
class ConditionProperty
{
	private $property;
	private $storage_unit;
	
	function ConditionProperty($property, $storage_unit = null)
	{
		$this->set_property($property);
		$this->set_storage_unit($storage_unit);
	}
	
	/**
	 * @return the $property
	 */
	public function get_property() 
	{
		return $this->property;
	}

	/**
	 * @return the $storage_unit
	 */
	public function get_storage_unit() 
	{
		return $this->storage_unit;
	}

	/**
	 * @param $property the $property to set
	 */
	public function set_property($property) 
	{
		$this->property = $property;
	}

	/**
	 * @param $storage_unit the $storage_unit to set
	 */
	public function set_storage_unit($storage_unit) 
	{
		$this->storage_unit = $storage_unit;
	}


	
}