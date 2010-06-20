<?php

/** @author Steven Willaert */

class InternshipOrganizerLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * InternshipOrganizerLocation properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_ORGANISATION_ID = 'organisation_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_ADDRESS = 'address';
    const PROPERTY_REGION_ID = 'region_id';
    //const PROPERTY_POSTCODE = 'postcode';
    //const PROPERTY_CITY = 'city';
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_ORGANISATION_ID, self :: PROPERTY_NAME, self :: PROPERTY_ADDRESS, self :: PROPERTY_REGION_ID, //						self::PROPERTY_POSTCODE, 
        //						self::PROPERTY_CITY,
        self :: PROPERTY_TELEPHONE, self :: PROPERTY_FAX, self :: PROPERTY_EMAIL, self :: PROPERTY_DESCRIPTION);
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    /**
     * Returns the id of this InternshipOrganizerLocation.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this InternshipOrganizerLocation.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the id of this InternshipOrganizerOrganisation.
     * @return the id.
     */
    function get_organisation_id()
    {
        return $this->get_default_property(self :: PROPERTY_ORGANISATION_ID);
    }

    /**
     * Sets the id of this InternshipOrganizerOrganisation.
     * @param id
     */
    function set_organisation_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ORGANISATION_ID, $id);
    }

    /**
     * Returns the name of this InternshipOrganizerLocation.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this InternshipOrganizerLocation.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the address of this InternshipOrganizerLocation.
     * @return the address.
     */
    function get_address()
    {
        return $this->get_default_property(self :: PROPERTY_ADDRESS);
    }

    /**
     * Sets the address of this InternshipOrganizerLocation.
     * @param address
     */
    function set_address($address)
    {
        $this->set_default_property(self :: PROPERTY_ADDRESS, $address);
    }

    /**
     * Returns the id of this InternshipOrganizerRegion.
     * @return the id.
     */
    function get_region_id()
    {
        return $this->get_default_property(self :: PROPERTY_REGION_ID);
    }

    /**
     * Sets the id of this InternshipOrganizerLocation.
     * @param id
     */
    function set_region_id($region_id)
    {
        $this->set_default_property(self :: PROPERTY_REGION_ID, $region_id);
    }

    /**
     * Returns the postcode of this InternshipOrganizerLocation.
     * @return the postcode.
     */
    function get_postcode()
    {
        return $this->get_default_property(self :: PROPERTY_POSTCODE);
    }

    /**
     * Sets the postcode of this InternshipOrganizerLocation.
     * @param postcode
     */
    function set_postcode($postcode)
    {
        $this->set_default_property(self :: PROPERTY_POSTCODE, $postcode);
    }

    /**
     * Returns the city of this InternshipOrganizerLocation.
     * @return the city.
     */
    function get_city()
    {
        return $this->get_default_property(self :: PROPERTY_CITY);
    }

    /**
     * Sets the city of this InternshipOrganizerLocation.
     * @param city
     */
    function set_city($city)
    {
        $this->set_default_property(self :: PROPERTY_CITY, $city);
    }

    /**
     * Returns the telephone of this InternshipOrganizerLocation.
     * @return the telephone.
     */
    function get_telephone()
    {
        return $this->get_default_property(self :: PROPERTY_TELEPHONE);
    }

    /**
     * Sets the telephone of this InternshipOrganizerLocation.
     * @param telephone
     */
    function set_telephone($telephone)
    {
        $this->set_default_property(self :: PROPERTY_TELEPHONE, $telephone);
    }

    /**
     * Returns the fax of this InternshipOrganizerLocation.
     * @return the fax.
     */
    function get_fax()
    {
        return $this->get_default_property(self :: PROPERTY_FAX);
    }

    /**
     * Sets the fax of this InternshipOrganizerLocation.
     * @param fax
     */
    function set_fax($fax)
    {
        $this->set_default_property(self :: PROPERTY_FAX, $fax);
    }

    /**
     * Returns the e-mail of this InternshipOrganizerLocation.
     * @return the e-mail.
     */
    function get_email()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL);
    }

    /**
     * Sets the e-mail of this InternshipOrganizerLocation.
     * @param e-mail
     */
    function set_email($email)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL, $email);
    }

    /**
     * Returns the description of this InternshipOrganizerLocation.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this InternshipOrganizerLocation.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function get_organisation()
    {
        return $this->get_data_manager()->retrieve_organisation($this->get_organisation_id());
    }

    function get_region()
    {
        return $this->get_data_manager()->retrieve_internship_organizer_region($this->get_region_id());
    }

    static function get_table_name()
    {
        return 'location';
        //		return Utilities::camelcase_to_underscores ( self::CLASS_NAME );
    

    }
}

?>