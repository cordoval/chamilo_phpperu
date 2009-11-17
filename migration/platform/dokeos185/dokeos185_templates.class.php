<?php
/**
 * $Id: dokeos185_templates.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_templates.class.php';

/**
 * This class presents a Dokeos185 templates
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Templates extends ImportTemplates
{
    private static $mgdm;
    
    /**
     * Dokeos185Templates properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_COURSE_CODE = 'course_code';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_REF_DOC = 'ref_doc';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Templates object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Templates($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_COURSE_CODE, self :: PROPERTY_USER_ID, self :: PROPERTY_REF_DOC);
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
     * Returns the id of this Dokeos185Templates.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the title of this Dokeos185Templates.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185Templates.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the course_code of this Dokeos185Templates.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Returns the user_id of this Dokeos185Templates.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the ref_doc of this Dokeos185Templates.
     * @return the ref_doc.
     */
    function get_ref_doc()
    {
        return $this->get_default_property(self :: PROPERTY_REF_DOC);
    }

    /**
     * Checks if template is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert templates
     * @param Array $array
     * @return Boolean
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Gets the templates of a course
     * @param Array $array
     * @return dokeos185templates
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'templates';
        $classname = 'Dokeos185Templates';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'templates';
        return $array;
    }
}

?>