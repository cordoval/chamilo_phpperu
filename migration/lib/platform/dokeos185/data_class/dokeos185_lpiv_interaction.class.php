<?php
/**
 * $Id: dokeos185_lpiv_interaction.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_lp_iv_interaction.class.php';

/**
 * This class presents a Dokeos185 lp_iv_interaction
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpIvInteraction extends Dokeos185MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185LpIvInteraction properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_ORDER_ID = 'order_id';
    const PROPERTY_LP_IV_ID = 'lp_iv_id';
    const PROPERTY_INTERACTION_ID = 'interaction_id';
    const PROPERTY_INTERACTION_TYPE = 'interaction_type';
    const PROPERTY_WEIGHTING = 'weighting';
    const PROPERTY_COMPLETION_TIME = 'completion_time';
    const PROPERTY_CORRECT_RESPONSES = 'correct_responses';
    const PROPERTY_STUDENT_RESPONSE = 'student_response';
    const PROPERTY_RESULT = 'result';
    const PROPERTY_LATENCY = 'latency';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185LpIvInteraction object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185LpIvInteraction($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_ORDER_ID, self :: PROPERTY_LP_IV_ID, self :: PROPERTY_INTERACTION_ID, self :: PROPERTY_INTERACTION_TYPE, self :: PROPERTY_WEIGHTING, self :: PROPERTY_COMPLETION_TIME, self :: PROPERTY_CORRECT_RESPONSES, self :: PROPERTY_STUDENT_RESPONSE, self :: PROPERTY_RESULT, self :: PROPERTY_LATENCY);
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
     * Returns the id of this Dokeos185LpIvInteraction.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the order_id of this Dokeos185LpIvInteraction.
     * @return the order_id.
     */
    function get_order_id()
    {
        return $this->get_default_property(self :: PROPERTY_ORDER_ID);
    }

    /**
     * Returns the lp_iv_id of this Dokeos185LpIvInteraction.
     * @return the lp_iv_id.
     */
    function get_lp_iv_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_IV_ID);
    }

    /**
     * Returns the interaction_id of this Dokeos185LpIvInteraction.
     * @return the interaction_id.
     */
    function get_interaction_id()
    {
        return $this->get_default_property(self :: PROPERTY_INTERACTION_ID);
    }

    /**
     * Returns the interaction_type of this Dokeos185LpIvInteraction.
     * @return the interaction_type.
     */
    function get_interaction_type()
    {
        return $this->get_default_property(self :: PROPERTY_INTERACTION_TYPE);
    }

    /**
     * Returns the weighting of this Dokeos185LpIvInteraction.
     * @return the weighting.
     */
    function get_weighting()
    {
        return $this->get_default_property(self :: PROPERTY_WEIGHTING);
    }

    /**
     * Returns the completion_time of this Dokeos185LpIvInteraction.
     * @return the completion_time.
     */
    function get_completion_time()
    {
        return $this->get_default_property(self :: PROPERTY_COMPLETION_TIME);
    }

    /**
     * Returns the correct_responses of this Dokeos185LpIvInteraction.
     * @return the correct_responses.
     */
    function get_correct_responses()
    {
        return $this->get_default_property(self :: PROPERTY_CORRECT_RESPONSES);
    }

    /**
     * Returns the student_response of this Dokeos185LpIvInteraction.
     * @return the student_response.
     */
    function get_student_response()
    {
        return $this->get_default_property(self :: PROPERTY_STUDENT_RESPONSE);
    }

    /**
     * Returns the result of this Dokeos185LpIvInteraction.
     * @return the result.
     */
    function get_result()
    {
        return $this->get_default_property(self :: PROPERTY_RESULT);
    }

    /**
     * Returns the latency of this Dokeos185LpIvInteraction.
     * @return the latency.
     */
    function get_latency()
    {
        return $this->get_default_property(self :: PROPERTY_LATENCY);
    }

    /**
     * Check if the lp iv interaction is valid
     * @param array $array the parameters for the validation
     * @return true if the lp iv interaction is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
    }

    /**
     * Convert to new lp iv interaction
     * @param array $array the parameters for the conversion
     * @return the new lp iv interaction
     */
    function convert_data
    {
        $course = $array['course'];
    }

    /**
     * Retrieve all lp iv interactions from the database
     * @param array $parameters parameters for the retrieval
     * @return array of lp iv interactions
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = $parameters['course']->get_db_name();
        $tablename = 'lp_iv_interaction';
        $classname = 'Dokeos185LpIvInteraction';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'lp_iv_interaction';
        return $array;
    }
}

?>