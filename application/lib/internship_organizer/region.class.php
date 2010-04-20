<?php
/**
 * internship_organizer
 */
require_once dirname(__FILE__) . '/internship_organizer_data_manager.class.php';
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
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION));
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
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Region.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
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