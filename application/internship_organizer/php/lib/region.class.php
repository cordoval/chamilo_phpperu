<?php
/**
 * internship_organizer
 */
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_data_manager.class.php';
/**
 * This class describes a Region data object
 * @author Sven Vanpoucke
 * @author Sven Vanhoecke
 */
class InternshipOrganizerRegion extends NestedTreeNode
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Region properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_CITY_NAME = 'region_city_name';
    const PROPERTY_ZIP_CODE = 'region_zip_code';
    const PROPERTY_DESCRIPTION = 'region_description';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_CITY_NAME, self :: PROPERTY_ZIP_CODE, self :: PROPERTY_DESCRIPTION));
    }

    /**
     * Returns the id of this Region.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this Region.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this Region.
     * @return the name.
     */
    function get_city_name()
    {
        return $this->get_default_property(self :: PROPERTY_CITY_NAME);
    }

    /**
     * Sets the name of this Region.
     * @param name
     */
    function set_city_name($city_name)
    {
        $this->set_default_property(self :: PROPERTY_CITY_NAME, $city_name);
    }

    /**
     * Returns the zip code of this Region.
     * @return the zip_code.
     */
    function get_zip_code()
    {
        return $this->get_default_property(self :: PROPERTY_ZIP_CODE);
    }

    /**
     * Sets the zip code of this Region.
     * @param zip_code
     */
    function set_zip_code($zip_code)
    {
        $this->set_default_property(self :: PROPERTY_ZIP_CODE, $zip_code);
    }

    /**
     * Returns the description of this Region.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Region.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    static function get_table_name()
    {
        //		 return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
        return 'region';
    }

    function get_data_manager()
    {
        return InternshipOrganizerDataManager :: get_instance();
    }

    function truncate()
    {
        return $this->get_data_manager()->truncate_region($this);
    }
}

?>