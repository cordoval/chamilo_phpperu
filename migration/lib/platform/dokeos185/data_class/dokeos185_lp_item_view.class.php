<?php
/**
 * $Id: dokeos185_lp_item_view.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_lp_item_view.class.php';

/**
 * This class presents a Dokeos185 lp_item_view
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpItemView extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185LpItemView properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_LP_ITEM_ID = 'lp_item_id';
    const PROPERTY_LP_VIEW_ID = 'lp_view_id';
    const PROPERTY_VIEW_COUNT = 'view_count';
    const PROPERTY_START_TIME = 'start_time';
    const PROPERTY_TOTAL_TIME = 'total_time';
    const PROPERTY_SCORE = 'score';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_SUSPEND_DATA = 'suspend_data';
    const PROPERTY_LESSON_LOCATION = 'lesson_location';
    const PROPERTY_CORE_EXIT = 'core_exit';
    const PROPERTY_MAX_SCORE = 'max_score';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185LpItemView object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185LpItemView($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_ITEM_ID, self :: PROPERTY_LP_VIEW_ID, self :: PROPERTY_VIEW_COUNT, self :: PROPERTY_START_TIME, self :: PROPERTY_TOTAL_TIME, self :: PROPERTY_SCORE, self :: PROPERTY_STATUS, self :: PROPERTY_SUSPEND_DATA, self :: PROPERTY_LESSON_LOCATION, self :: PROPERTY_CORE_EXIT, self :: PROPERTY_MAX_SCORE);
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
     * Returns the id of this Dokeos185LpItemView.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the lp_item_id of this Dokeos185LpItemView.
     * @return the lp_item_id.
     */
    function get_lp_item_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_ITEM_ID);
    }

    /**
     * Returns the lp_view_id of this Dokeos185LpItemView.
     * @return the lp_view_id.
     */
    function get_lp_view_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_VIEW_ID);
    }

    /**
     * Returns the view_count of this Dokeos185LpItemView.
     * @return the view_count.
     */
    function get_view_count()
    {
        return $this->get_default_property(self :: PROPERTY_VIEW_COUNT);
    }

    /**
     * Returns the start_time of this Dokeos185LpItemView.
     * @return the start_time.
     */
    function get_start_time()
    {
        return $this->get_default_property(self :: PROPERTY_START_TIME);
    }

    /**
     * Returns the total_time of this Dokeos185LpItemView.
     * @return the total_time.
     */
    function get_total_time()
    {
        return $this->get_default_property(self :: PROPERTY_TOTAL_TIME);
    }

    /**
     * Returns the score of this Dokeos185LpItemView.
     * @return the score.
     */
    function get_score()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE);
    }

    /**
     * Returns the status of this Dokeos185LpItemView.
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the suspend_data of this Dokeos185LpItemView.
     * @return the suspend_data.
     */
    function get_suspend_data()
    {
        return $this->get_default_property(self :: PROPERTY_SUSPEND_DATA);
    }

    /**
     * Returns the lesson_location of this Dokeos185LpItemView.
     * @return the lesson_location.
     */
    function get_lesson_location()
    {
        return $this->get_default_property(self :: PROPERTY_LESSON_LOCATION);
    }

    /**
     * Returns the core_exit of this Dokeos185LpItemView.
     * @return the core_exit.
     */
    function get_core_exit()
    {
        return $this->get_default_property(self :: PROPERTY_CORE_EXIT);
    }

    /**
     * Returns the max_score of this Dokeos185LpItemView.
     * @return the max_score.
     */
    function get_max_score()
    {
        return $this->get_default_property(self :: PROPERTY_MAX_SCORE);
    }

    /**
     * Check if the lp item view is valid
     * @param array $array the parameters for the validation
     * @return true if the lp item view is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new lp item view
     * @param array $array the parameters for the conversion
     * @return the new lp item view
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all lp item views from the database
     * @param array $parameters parameters for the retrieval
     * @return array of lp item views
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'lp_item_view';
        $classname = 'Dokeos185LpItemView';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'lp_item_view';
        return $array;
    }
}

?>