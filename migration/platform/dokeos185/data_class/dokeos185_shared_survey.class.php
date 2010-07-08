<?php
/**
 * $Id: dokeos185_shared_survey.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_shared_survey.class.php';

/**
 * This class presents a Dokeos185 shared_survey
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SharedSurvey extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185SharedSurvey properties
     */
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_CODE = 'code';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SUBTITLE = 'subtitle';
    const PROPERTY_AUTHOR = 'author';
    const PROPERTY_LANG = 'lang';
    const PROPERTY_TEMPLATE = 'template';
    const PROPERTY_INTRO = 'intro';
    const PROPERTY_SURVEYTHANKS = 'surveythanks';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_COURSE_CODE = 'course_code';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185SharedSurvey object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185SharedSurvey($defaultProperties = array ())
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
        return array(self :: PROPERTY_SURVEY_ID, self :: PROPERTY_CODE, self :: PROPERTY_TITLE, self :: PROPERTY_SUBTITLE, self :: PROPERTY_AUTHOR, self :: PROPERTY_LANG, self :: PROPERTY_TEMPLATE, self :: PROPERTY_INTRO, self :: PROPERTY_SURVEYTHANKS, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_COURSE_CODE);
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
     * Returns the survey_id of this Dokeos185SharedSurvey.
     * @return the survey_id.
     */
    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    /**
     * Returns the code of this Dokeos185SharedSurvey.
     * @return the code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the title of this Dokeos185SharedSurvey.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the subtitle of this Dokeos185SharedSurvey.
     * @return the subtitle.
     */
    function get_subtitle()
    {
        return $this->get_default_property(self :: PROPERTY_SUBTITLE);
    }

    /**
     * Returns the author of this Dokeos185SharedSurvey.
     * @return the author.
     */
    function get_author()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR);
    }

    /**
     * Returns the lang of this Dokeos185SharedSurvey.
     * @return the lang.
     */
    function get_lang()
    {
        return $this->get_default_property(self :: PROPERTY_LANG);
    }

    /**
     * Returns the template of this Dokeos185SharedSurvey.
     * @return the template.
     */
    function get_template()
    {
        return $this->get_default_property(self :: PROPERTY_TEMPLATE);
    }

    /**
     * Returns the intro of this Dokeos185SharedSurvey.
     * @return the intro.
     */
    function get_intro()
    {
        return $this->get_default_property(self :: PROPERTY_INTRO);
    }

    /**
     * Returns the surveythanks of this Dokeos185SharedSurvey.
     * @return the surveythanks.
     */
    function get_surveythanks()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEYTHANKS);
    }

    /**
     * Returns the creation_date of this Dokeos185SharedSurvey.
     * @return the creation_date.
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the course_code of this Dokeos185SharedSurvey.
     * @return the course_code.
     */
    function get_course_code()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
    }

    /**
     * Checks if a shared survey is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
    
    }

    /**
     * migrate shared survey, sets category
     * @param Array $array
     * @return
     */
    function convert_data
    {
    
    }

    /**
     * Gets all the shared surveys of a course
     * @param Array $array
     * @return
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'shared_survey';
        $classname = 'Dokeos185SharedSurvey';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'shared_survey';
        return $array;
    }
}

?>