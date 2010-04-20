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
	const PROPERTY_ADDRESS = 'address';
	const PROPERTY_POSTCODE = 'postcode';
	const PROPERTY_CITY = 'city';
	const PROPERTY_TELEPHONE = 'telephone';
	const PROPERTY_FAX = 'fax';
	const PROPERTY_EMAIL = 'email';
	const PROPERTY_DESCRIPTION = 'description';
	

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (	self :: PROPERTY_ID, 
						self :: PROPERTY_NAME,
						self :: PROPERTY_ADDRESS,
						self :: PROPERTY_POSTCODE,
						self :: PROPERTY_CITY,
						self :: PROPERTY_TELEPHONE,
						self :: PROPERTY_FAX,
						self :: PROPERTY_EMAIL,
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
	 * Returns the address of this Organisation.
	 * @return the address.
	 */
	function get_address()
	{
		return $this->get_default_property(self :: PROPERTY_ADDRESS);
	}

	/**
	 * Sets the address of this Organisation.
	 * @param address
	 */
	function set_address($address)
	{
		$this->set_default_property(self :: PROPERTY_ADDRESS, $address);
	}
	
/**
	 * Returns the postcode of this Organisation.
	 * @return the postcode.
	 */
	function get_postcode()
	{
		return $this->get_default_property(self :: PROPERTY_POSTCODE);
	}

	/**
	 * Sets the postcode of this Organisation.
	 * @param postcode
	 */
	function set_postcode($postcode)
	{
		$this->set_default_property(self :: PROPERTY_POSTCODE, $postcode);
	}
	
/**
	 * Returns the city of this Organisation.
	 * @return the city.
	 */
	function get_city()
	{
		return $this->get_default_property(self :: PROPERTY_CITY);
	}

	/**
	 * Sets the city of this Organisation.
	 * @param city
	 */
	function set_city($city)
	{
		$this->set_default_property(self :: PROPERTY_CITY, $city);
	}
	
/**
	 * Returns the telephone number of this Organisation.
	 * @return the telephone number.
	 */
	function get_telephone()
	{
		return $this->get_default_property(self :: PROPERTY_TELEPHONE);
	}

	/**
	 * Sets the telephone number of this Organisation.
	 * @param telephone number
	 */
	function set_telephone($telephone)
	{
		$this->set_default_property(self :: PROPERTY_TELEPHONE, $telephone);
	}
	
/**
	 * Returns the fax number of this Organisation.
	 * @return fax.
	 */
	function get_fax()
	{
		return $this->get_default_property(self :: PROPERTY_FAX);
	}

	/**
	 * Sets the fax number of this Organisation.
	 * @param fax
	 */
	function set_fax($fax)
	{
		$this->set_default_property(self :: PROPERTY_FAX, $fax);
	}
	
/**
	 * Returns the e-mail of this Organisation.
	 * @return the e-mail.
	 */
	function get_email()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL);
	}

	/**
	 * Sets the e-mail of this Organisation.
	 * @param e-mail
	 */
	function set_email($email)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL, $email);
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
}

?>