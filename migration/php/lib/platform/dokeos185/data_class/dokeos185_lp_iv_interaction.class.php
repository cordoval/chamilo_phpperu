<?php
/**
 * $Id: dokeos185_lpiv_interaction.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once Path :: get_web_application_path('weblcms') . '/trackers/weblcms_lpi_attempt_interaction_tracker.class.php';
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 lp_iv_interaction
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpIvInteraction extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'lp_iv_interaction';
    
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
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_ORDER_ID, self :: PROPERTY_LP_IV_ID, self :: PROPERTY_INTERACTION_ID, self :: PROPERTY_INTERACTION_TYPE, self :: PROPERTY_WEIGHTING, self :: PROPERTY_COMPLETION_TIME, self :: PROPERTY_CORRECT_RESPONSES, self :: PROPERTY_STUDENT_RESPONSE, self :: PROPERTY_RESULT, self :: PROPERTY_LATENCY);
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
     * @return true if the lp iv interaction is valid 
     */
    function is_valid()
    {
        $new_lp_item_view_id = $this->get_id_reference($this->get_lp_iv_id(), $this->get_database_name() . '.lp_item_view');
        
        if (! $this->get_id() || ! $new_lp_item_view_id)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'learning_path_item_view_interaction', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new lp iv interaction
     */
    function convert_data()
    {
        $new_lp_item_view_id = $this->get_id_reference($this->get_lp_iv_id(), $this->get_database_name() . '.lp_item_view');
    	
    	$tracker = new WeblcmsLpiAttemptInteractionTracker();
    	$tracker->set_lpi_view_id($new_lp_item_view_id);
    	$tracker->set_interaction_id($this->get_interaction_id());
    	$tracker->set_interaction_type($this->get_interaction_type());
    	$tracker->set_weight($this->get_weighting());
    	$tracker->set_completion_time($this->get_completion_time());
    	$tracker->set_correct_responses($this->get_correct_responses());
    	$tracker->set_student_responses($this->get_student_response());
    	$tracker->set_result($this->get_result());
    	$tracker->set_latency($this->get_latency());
    	$tracker->set_display_order($this->get_order_id());
    	$tracker->create();
    	
    	$this->create_id_reference($this->get_id(), $tracker->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'learning_path_view_interaction', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $tracker->get_id())));
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}

?>