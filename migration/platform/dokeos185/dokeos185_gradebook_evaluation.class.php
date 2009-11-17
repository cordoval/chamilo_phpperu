<?php
/**
 * $Id: dokeos185_gradebook_evaluation.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_gradebook_evaluation.class.php';

/**
 * This class presents a Dokeos185 gradebook_evaluation
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookEvaluation extends ImportGradebookEvaluation
{
    private static $mgdm;
    
    /**
     * Dokeos185GradebookEvaluation properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_WEIGHT = 'weight';
    const PROPERTY_MAX = 'max';
    const PROPERTY_VISIBLE = 'visible';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185GradebookEvaluation object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185GradebookEvaluation($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_USER_ID, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_DATE, self :: PROPERTY_WEIGHT, self :: PROPERTY_MAX, self :: PROPERTY_VISIBLE);
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
     * Returns the id of this Dokeos185GradebookEvaluation.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the name of this Dokeos185GradebookEvaluation.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the description of this Dokeos185GradebookEvaluation.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the user_id of this Dokeos185GradebookEvaluation.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the course_code of this Dokeos185GradebookEvaluation.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the category_id of this Dokeos185GradebookEvaluation.
     * @return the category_id.
     */
    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    /**
     * Returns the date of this Dokeos185GradebookEvaluation.
     * @return the date.
     */
    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    /**
     * Returns the weight of this Dokeos185GradebookEvaluation.
     * @return the weight.
     */
    function get_weight()
    {
        return $this->get_default_property(self :: PROPERTY_WEIGHT);
    }

    /**
     * Returns the max of this Dokeos185GradebookEvaluation.
     * @return the max.
     */
    function get_max()
    {
        return $this->get_default_property(self :: PROPERTY_MAX);
    }

    /**
     * Returns the visible of this Dokeos185GradebookEvaluation.
     * @return the visible.
     */
    function get_visible()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBLE);
    }

    /**
     * Check if the gradebook evaluation is valid
     * @param array $array the parameters for the validation
     * @return true if the gradebook evaluation is valid 
     */
    function is_valid($array)
    {
    
    }

    /**
     * Convert to new gradebook evaluation
     * @param array $array the parameters for the conversion
     * @return the new gradebook evaluation
     */
    function convert_to_lcms($array)
    {
    
    }

    /**
     * Retrieve all gradebook evaluations from the database
     * @param array $parameters parameters for the retrieval
     * @return array of gradebook evaluations
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'gradebook_evaluation';
        $classname = 'Dokeos185GradebookEvaluation';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'gradebook_evaluation';
        return $array;
    }
}

?>