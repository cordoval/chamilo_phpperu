<?php
/**
 * $Id: dokeos185_track_eexercices.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_eexercices.class.php';

/**
 * This class presents a Dokeos185 track_e_exercices
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEExercices extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185TrackEExercices properties
     */
    const PROPERTY_EXE_ID = 'exe_id';
    const PROPERTY_EXE_USER_ID = 'exe_user_id';
    const PROPERTY_EXE_DATE = 'exe_date';
    const PROPERTY_EXE_COURS_ID = 'exe_cours_id';
    const PROPERTY_EXE_EXO_ID = 'exe_exo_id';
    const PROPERTY_EXE_RESULT = 'exe_result';
    const PROPERTY_EXE_WEIGHTING = 'exe_weighting';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEExercices object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEExercices($defaultProperties = array ())
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
        return array(self :: PROPERTY_EXE_ID, self :: PROPERTY_EXE_USER_ID, self :: PROPERTY_EXE_DATE, self :: PROPERTY_EXE_COURS_ID, self :: PROPERTY_EXE_EXO_ID, self :: PROPERTY_EXE_RESULT, self :: PROPERTY_EXE_WEIGHTING);
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
     * Returns the exe_id of this Dokeos185TrackEExercices.
     * @return the exe_id.
     */
    function get_exe_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_ID);
    }

    /**
     * Returns the exe_user_id of this Dokeos185TrackEExercices.
     * @return the exe_user_id.
     */
    function get_exe_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_USER_ID);
    }

    /**
     * Returns the exe_date of this Dokeos185TrackEExercices.
     * @return the exe_date.
     */
    function get_exe_date()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_DATE);
    }

    /**
     * Returns the exe_cours_id of this Dokeos185TrackEExercices.
     * @return the exe_cours_id.
     */
    function get_exe_cours_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_COURS_ID);
    }

    /**
     * Returns the exe_exo_id of this Dokeos185TrackEExercices.
     * @return the exe_exo_id.
     */
    function get_exe_exo_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_EXO_ID);
    }

    /**
     * Returns the exe_result of this Dokeos185TrackEExercices.
     * @return the exe_result.
     */
    function get_exe_result()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_RESULT);
    }

    /**
     * Returns the exe_weighting of this Dokeos185TrackEExercices.
     * @return the exe_weighting.
     */
    function get_exe_weighting()
    {
        return $this->get_default_property(self :: PROPERTY_EXE_WEIGHTING);
    }

    /**
     * Validation checks
     * @param Array $array
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convertion
     * @param Array $array
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Gets all the trackers
     * @param Array $array
     * @return Array
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        $db = 'statistics_database';
        $tablename = 'track_e_exercices';
        $classname = 'Dokeos185TrackEExercices';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_e_exercices';
        return $array;
    }
}

?>