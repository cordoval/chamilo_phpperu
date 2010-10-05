<?php
/**
 * $Id: dokeos185_assignment.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_assignment.class.php';

/**
 * This class presents a Dokeos185 assignment
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Assignment extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185Assignment properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_DEF_SUBMISSION_VISIBILITY = 'def_submission_visibility';
    const PROPERTY_ASSIGNMENT_TYPE = 'assignment_type';
    const PROPERTY_AUTHORIZED_CONTENT = 'authorized_content';
    const PROPERTY_ALLOW_LATE_UPLOAD = 'allow_late_upload';
    const PROPERTY_FEEDBACK_TEXT = 'feedback_text';
    const PROPERTY_FEEDBACK_DOC_PATH = 'feedback_doc_path';
    const PROPERTY_FEEDBACK_VISIBLE = 'feedback_visible';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Assignment object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Assignment($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_DEF_SUBMISSION_VISIBILITY, self :: PROPERTY_ASSIGNMENT_TYPE, self :: PROPERTY_AUTHORIZED_CONTENT, self :: PROPERTY_ALLOW_LATE_UPLOAD, self :: PROPERTY_FEEDBACK_TEXT, self :: PROPERTY_FEEDBACK_DOC_PATH, self :: PROPERTY_FEEDBACK_VISIBLE);
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
     * Returns the id of this Dokeos185Assignment.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this Dokeos185Assignment.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185Assignment.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the def_submission_visibility of this Dokeos185Assignment.
     * @return the def_submission_visibility.
     */
    function get_def_submission_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_DEF_SUBMISSION_VISIBILITY);
    }

    /**
     * Returns the assignment_type of this Dokeos185Assignment.
     * @return the assignment_type.
     */
    function get_assignment_type()
    {
        return $this->get_default_property(self :: PROPERTY_ASSIGNMENT_TYPE);
    }

    /**
     * Returns the authorized_content of this Dokeos185Assignment.
     * @return the authorized_content.
     */
    function get_authorized_content()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHORIZED_CONTENT);
    }

    /**
     * Returns the allow_late_upload of this Dokeos185Assignment.
     * @return the allow_late_upload.
     */
    function get_allow_late_upload()
    {
        return $this->get_default_property(self :: PROPERTY_ALLOW_LATE_UPLOAD);
    }

    /**
     * Returns the feedback_text of this Dokeos185Assignment.
     * @return the feedback_text.
     */
    function get_feedback_text()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_TEXT);
    }

    /**
     * Returns the feedback_doc_path of this Dokeos185Assignment.
     * @return the feedback_doc_path.
     */
    function get_feedback_doc_path()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_DOC_PATH);
    }

    /**
     * Returns the feedback_visible of this Dokeos185Assignment.
     * @return the feedback_visible.
     */
    function get_feedback_visible()
    {
        return $this->get_default_property(self :: PROPERTY_FEEDBACK_VISIBLE);
    }

    /**
     * Check if the assignment is valid
     * @param array $array the parameters for the validation
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new assignment 
     * Create assignment
     * @param array $array the parameters for the conversion
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all assignments from the database
     * @param array $parameters parameters for the retrieval
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'assignment';
        $classname = 'Dokeos185Assignment';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'assignment';
        return $array;
    }
}

?>