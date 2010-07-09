<?php
/**
 * $Id: dokeos185_track_ehotspot.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_ehotspot.class.php';

/**
 * This class presents a Dokeos185 track_e_hotspot
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEHotspot extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185TrackEHotspot properties
     */
    const PROPERTY_HOTSPOT_ID = 'hotspot_id';
    const PROPERTY_HOTSPOT_USER_ID = 'hotspot_user_id';
    const PROPERTY_HOTSPOT_COURSE_CODE = 'hotspot_course_code';
    const PROPERTY_HOTSPOT_EXE_ID = 'hotspot_exe_id';
    const PROPERTY_HOTSPOT_QUESTION_ID = 'hotspot_question_id';
    const PROPERTY_HOTSPOT_ANSWER_ID = 'hotspot_answer_id';
    const PROPERTY_HOTSPOT_CORRECT = 'hotspot_correct';
    const PROPERTY_HOTSPOT_COORDINATE = 'hotspot_coordinate';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEHotspot object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEHotspot($defaultProperties = array ())
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
        return array(self :: PROPERTY_HOTSPOT_ID, self :: PROPERTY_HOTSPOT_USER_ID, self :: PROPERTY_HOTSPOT_COURSE_CODE, self :: PROPERTY_HOTSPOT_EXE_ID, self :: PROPERTY_HOTSPOT_QUESTION_ID, self :: PROPERTY_HOTSPOT_ANSWER_ID, self :: PROPERTY_HOTSPOT_CORRECT, self :: PROPERTY_HOTSPOT_COORDINATE);
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
     * Returns the hotspot_id of this Dokeos185TrackEHotspot.
     * @return the hotspot_id.
     */
    function get_hotspot_id()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_ID);
    }

    /**
     * Returns the hotspot_user_id of this Dokeos185TrackEHotspot.
     * @return the hotspot_user_id.
     */
    function get_hotspot_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_USER_ID);
    }

    /**
     * Returns the hotspot_course_code of this Dokeos185TrackEHotspot.
     * @return the hotspot_course_code.
     */
    function get_hotspot_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_COURSE_CODE);
    }

    /**
     * Returns the hotspot_exe_id of this Dokeos185TrackEHotspot.
     * @return the hotspot_exe_id.
     */
    function get_hotspot_exe_id()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_EXE_ID);
    }

    /**
     * Returns the hotspot_question_id of this Dokeos185TrackEHotspot.
     * @return the hotspot_question_id.
     */
    function get_hotspot_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_QUESTION_ID);
    }

    /**
     * Returns the hotspot_answer_id of this Dokeos185TrackEHotspot.
     * @return the hotspot_answer_id.
     */
    function get_hotspot_answer_id()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_ANSWER_ID);
    }

    /**
     * Returns the hotspot_correct of this Dokeos185TrackEHotspot.
     * @return the hotspot_correct.
     */
    function get_hotspot_correct()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_CORRECT);
    }

    /**
     * Returns the hotspot_coordinate of this Dokeos185TrackEHotspot.
     * @return the hotspot_coordinate.
     */
    function get_hotspot_coordinate()
    {
        return $this->get_default_property(self :: PROPERTY_HOTSPOT_COORDINATE);
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
        $tablename = 'track_e_hotspot';
        $classname = 'Dokeos185TrackEHotspot';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_e_hotspot';
        return $array;
    }
}

?>