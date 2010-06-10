<?php 

/** @author Steven Willaert */

class InternshipOrganizerOrganisation extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Organisation properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
//	const PROPERTY_ADDRESS = 'address';
//	const PROPERTY_POSTCODE = 'postcode';
//	const PROPERTY_CITY = 'city';
//	const PROPERTY_TELEPHONE = 'telephone';
//	const PROPERTY_FAX = 'fax';
//	const PROPERTY_EMAIL = 'email';
	const PROPERTY_DESCRIPTION = 'description';
	

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (	self :: PROPERTY_ID, 
						self :: PROPERTY_NAME,
//						self :: PROPERTY_ADDRESS,
//						self :: PROPERTY_POSTCODE,
//						self :: PROPERTY_CITY,
//						self :: PROPERTY_TELEPHONE,
//						self :: PROPERTY_FAX,
//						self :: PROPERTY_EMAIL,
						self :: PROPERTY_DESCRIPTION);
	}

	function get_data_manager()
	{
		return InternshipOrganizerDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Organisation.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Organisation.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this Organisation.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this Organisation.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}


	/**
	 * Returns the description of this Organisation.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Organisation.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}


	static function get_table_name()
	{
		return 'organisation';
	}
	
    function count_locations()
    {
        $locations = $this->get_locations();
        
        return count($locations);
    }
    
    function get_locations()
    {
        $dm = $this->get_data_manager();
        
        $organisation_id = $this->get_id();
        
//        if ($include_subcategories)
//        {
//            $subcategories = $dm->nested_tree_get_children($this, $recursive_subcategories);
//            
//            while ($subcategory = $subcategories->next_result())
//            {
//                $categories[] = $subcategory->get_id();
//            }
//        }
        
        $condition = new InCondition(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $organisation_id);
        $organisation_rel_locations = $dm->retrieve_organisation_rel_locations($condition);
        $locations = array();
        
        while ($organisation_rel_location = $organisation_rel_locations->next_result())
        {
            $location_id = $organisation_rel_location->get_id();
            if (! in_array($location_id, $locations))
            {
                $locations[] = $location_id;
            }
        }
        
        return $locations;
    }
}

?>