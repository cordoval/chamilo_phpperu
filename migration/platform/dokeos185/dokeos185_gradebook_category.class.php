<?php
/**
 * $Id: dokeos185_gradebook_category.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_gradebook_category.class.php';

/**
 * This class presents a Dokeos185 gradebook_category
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookCategory extends ImportGradebookCategory
{
    private static $mgdm;
    
    /**
     * Dokeos185GradebookCategory properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_VISIBLE = 'visible';
    const PROPERTY_CERTIF_MIN_SCORE = 'certif_min_score';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185GradebookCategory object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185GradebookCategory($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_USER_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_PARENT_ID, self :: PROPERTY_WEIGHT, self :: PROPERTY_VISIBLE, self :: PROPERTY_CERTIF_MIN_SCORE);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this Dokeos185GradebookCategory.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this Dokeos185GradebookCategory.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the description of this Dokeos185GradebookCategory.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the user_id of this Dokeos185GradebookCategory.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the course_code of this Dokeos185GradebookCategory.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the parent_id of this Dokeos185GradebookCategory.
     * @return the parent_id.
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Returns the weight of this Dokeos185GradebookCategory.
     * @return the weight.
     */
    function get_weight()
    {
        return $this->get_default_property(self :: PROPERTY_WEIGHT);
    }

    /**
     * Returns the visible of this Dokeos185GradebookCategory.
     * @return the visible.
     */
    function get_visible()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE);
    }

    /**
     * Returns the certif_min_score of this Dokeos185GradebookCategory.
     * @return the certif_min_score.
     */
    function get_certif_min_score()
    {
        return $this->get_default_property(self :: PROPERTY_CERTIF_MIN_SCORE);
    }

    /**
     * Check if the gradebook category is valid
     * @param array $array the parameters for the validation
     * @return true if the gradebook category is valid 
     */
    function is_valid($array)
    {
    
    }

    /**
     * Convert to new gradebook category
     * @param array $array the parameters for the conversion
     * @return the new gradebook category
     */
    
    function convert_to_lcms($array)
    {
    
    }

    /**
     * Retrieve all gradebook categories from the database
     * @param array $parameters parameters for the retrieval
     * @return array of gradebook categories
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'gradebook_category';
        $classname = 'Dokeos185GradebookCategory';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'gradebook_category';
        return $array;
    }
}

?>