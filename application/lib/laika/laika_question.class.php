<?php
/**
 * $Id: laika_question.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

class LaikaQuestion
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'question';
    
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_SCALE_ID = 'scale_id';
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_CORRECTION = 'correction';
    
    private $defaultProperties;

    function __construct($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets the value of a given default property
     * @param String $name the name of the default property
     * @return Object $value the new value
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gives a value to a given default property
     * @param String $name the name of the default property
     * @param Object $value the new value
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Retrieves the default properties of this class
     * @return Array of Objects
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Sets the default properties of this class
     * @param Array Of Objects $defaultProperties
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Method to check whether a certain name is a default property name
     * @param String $name
     * @return 
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Get the default property names
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_WEIGHT, self :: PROPERTY_CORRECTION, self :: PROPERTY_SCALE_ID);
    }

    function create()
    {
        $ldm = LaikaDataManager :: get_instance();
        $id = $ldm->get_next_laika_question_id();
        $this->set_id($id);
        return $ldm->create_laika_question($this);
    }

    function update()
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->update_laika_question($this);
    }

    function delete()
    {
        $ldm = LaikaDataManager :: get_instance();
        return $ldm->delete_laika_question($this);
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function get_scale_id()
    {
        return $this->get_default_property(self :: PROPERTY_SCALE_ID);
    }

    function set_scale_id($scale_id)
    {
        $this->set_default_property(self :: PROPERTY_SCALE_ID, $scale_id);
    }

    function get_weight()
    {
        return $this->get_default_property(self :: PROPERTY_WEIGHT);
    }

    function set_weight($weight)
    {
        $this->set_default_property(self :: PROPERTY_WEIGHT, $weight);
    }

    function get_correction()
    {
        return $this->get_default_property(self :: PROPERTY_CORRECTION);
    }

    function set_correction($correction)
    {
        $this->set_default_property(self :: PROPERTY_CORRECTION, $correction);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>