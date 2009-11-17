<?php
/**
 * $Id: dokeos185_lp_view.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_lp_view.class.php';

/**
 * This class presents a Dokeos185 lp_view
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpView extends ImportLpView
{
    private static $mgdm;
    
    /**
     * Dokeos185LpView properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_LP_ID = 'lp_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_VIEW_COUNT = 'view_count';
    const PROPERTY_LAST_ITEM = 'last_item';
    const PROPERTY_PROGRESS = 'progress';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185LpView object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185LpView($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_VIEW_COUNT, self :: PROPERTY_LAST_ITEM, self :: PROPERTY_PROGRESS);
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
     * Returns the id of this Dokeos185LpView.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the lp_id of this Dokeos185LpView.
     * @return the lp_id.
     */
    function get_lp_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_ID);
    }

    /**
     * Returns the user_id of this Dokeos185LpView.
     * @return the user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the view_count of this Dokeos185LpView.
     * @return the view_count.
     */
    function get_view_count()
    {
        return $this->get_default_property(self :: PROPERTY_VIEW_COUNT);
    }

    /**
     * Returns the last_item of this Dokeos185LpView.
     * @return the last_item.
     */
    function get_last_item()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_ITEM);
    }

    /**
     * Returns the progress of this Dokeos185LpView.
     * @return the progress.
     */
    function get_progress()
    {
        return $this->get_default_property(self :: PROPERTY_PROGRESS);
    }

    /**
     * Check if the lp view is valid
     * @param array $array the parameters for the validation
     * @return true if the lp view is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new lp view
     * @param array $array the parameters for the conversion
     * @return the new lp view
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all lp views from the database
     * @param array $parameters parameters for the retrieval
     * @return array of lp views
     */
    static function get_all($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'lp_view';
        $classname = 'Dokeos185LpView';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'lp_view';
        return $array;
    }
}

?>