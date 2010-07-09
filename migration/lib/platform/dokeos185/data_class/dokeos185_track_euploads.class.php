<?php
/**
 * $Id: dokeos185_track_euploads.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_euploads.class.php';

/**
 * This class presents a Dokeos185 track_e_uploads
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEUploads extends MigrationDataClass
{
    
    /**
     * Dokeos185TrackEUploads properties
     */
    const PROPERTY_UPLOAD_ID = 'upload_id';
    const PROPERTY_UPLOAD_USER_ID = 'upload_user_id';
    const PROPERTY_UPLOAD_DATE = 'upload_date';
    const PROPERTY_UPLOAD_COURS_ID = 'upload_cours_id';
    const PROPERTY_UPLOAD_WORK_ID = 'upload_work_id';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185TrackEUploads object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185TrackEUploads($defaultProperties = array ())
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
        return array(self :: PROPERTY_UPLOAD_ID, self :: PROPERTY_UPLOAD_USER_ID, self :: PROPERTY_UPLOAD_DATE, self :: PROPERTY_UPLOAD_COURS_ID, self :: PROPERTY_UPLOAD_WORK_ID);
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
     * Returns the upload_id of this Dokeos185TrackEUploads.
     * @return the upload_id.
     */
    function get_upload_id()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_ID);
    }

    /**
     * Returns the upload_user_id of this Dokeos185TrackEUploads.
     * @return the upload_user_id.
     */
    function get_upload_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_USER_ID);
    }

    /**
     * Returns the upload_date of this Dokeos185TrackEUploads.
     * @return the upload_date.
     */
    function get_upload_date()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_DATE);
    }

    /**
     * Returns the upload_cours_id of this Dokeos185TrackEUploads.
     * @return the upload_cours_id.
     */
    function get_upload_cours_id()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_COURS_ID);
    }

    /**
     * Returns the upload_work_id of this Dokeos185TrackEUploads.
     * @return the upload_work_id.
     */
    function get_upload_work_id()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_WORK_ID);
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
        $tablename = 'track_e_uploads';
        $classname = 'Dokeos185TrackEUploads';
        
        return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'statistics_database';
        $array['table'] = 'track_e_uploads';
        return $array;
    }
}

?>