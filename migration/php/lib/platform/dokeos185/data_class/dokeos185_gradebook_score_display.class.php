<?php
/**
 * $Id: dokeos185_gradebook_score_display.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_gradebook_score_display.class.php';

/**
 * This class presents a Dokeos185 gradebook_score_display
 *
 * @author Sven Vanpoucke
 */
class Dokeos185GradebookScoreDisplay extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185GradebookScoreDisplay properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_DISPLAY = 'display';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185GradebookScoreDisplay object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185GradebookScoreDisplay($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_SCORE, self :: PROPERTY_DISPLAY);
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
     * Returns the id of this Dokeos185GradebookScoreDisplay.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the score of this Dokeos185GradebookScoreDisplay.
     * @return the score.
     */
    function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    /**
     * Returns the display of this Dokeos185GradebookScoreDisplay.
     * @return the display.
     */
    function get_display()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY);
    }

    /**
     * Check if the gradebook score display is valid
     * @param array $array the parameters for the validation
     * @return true if the gradebook score display is valid 
     */
    function is_valid($array)
    {
    
    }

    /**
     * Convert to new gradebook score display
     * @param array $array the parameters for the conversion
     * @return the new gradebook score display
     */
    function convert_data
    {
    
    }

    /**
     * Retrieve all gradebook score displays from the database
     * @param array $parameters parameters for the retrieval
     * @return array of gradebook score displays
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'gradebook_score_display';
        $classname = 'Dokeos185GradebookScoreDisplay';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'gradebook_score_display';
        return $array;
    }
}

?>