<?php
/**
 * $Id: dokeos185_lpiv_objective.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once Path :: get_web_application_path('weblcms') . '/trackers/weblcms_lpi_attempt_objective_tracker.class.php';
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 lp_iv_objective
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpIvObjective extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'lp_iv_objective';
    
    /**
     * Dokeos185LpIvObjective properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_LP_IV_ID = 'lp_iv_id';
    const PROPERTY_ORDER_ID = 'order_id';
    const PROPERTY_OBJECTIVE_ID = 'objective_id';
    const PROPERTY_SCORE_RAW = 'score_raw';
    const PROPERTY_SCORE_MAX = 'score_max';
    const PROPERTY_SCORE_MIN = 'score_min';
    const PROPERTY_STATUS = 'status';
    
    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_IV_ID, self :: PROPERTY_ORDER_ID, self :: PROPERTY_OBJECTIVE_ID, self :: PROPERTY_SCORE_RAW, self :: PROPERTY_SCORE_MAX, self :: PROPERTY_SCORE_MIN, self :: PROPERTY_STATUS);
    }

    /**
     * Returns the id of this Dokeos185LpIvObjective.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the lp_iv_id of this Dokeos185LpIvObjective.
     * @return the lp_iv_id.
     */
    function get_lp_iv_id()
    {
        return $this->get_default_property(self :: PROPERTY_LP_IV_ID);
    }

    /**
     * Returns the order_id of this Dokeos185LpIvObjective.
     * @return the order_id.
     */
    function get_order_id()
    {
        return $this->get_default_property(self :: PROPERTY_ORDER_ID);
    }

    /**
     * Returns the objective_id of this Dokeos185LpIvObjective.
     * @return the objective_id.
     */
    function get_objective_id()
    {
        return $this->get_default_property(self :: PROPERTY_OBJECTIVE_ID);
    }

    /**
     * Returns the score_raw of this Dokeos185LpIvObjective.
     * @return the score_raw.
     */
    function get_score_raw()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE_RAW);
    }

    /**
     * Returns the score_max of this Dokeos185LpIvObjective.
     * @return the score_max.
     */
    function get_score_max()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE_MAX);
    }

    /**
     * Returns the score_min of this Dokeos185LpIvObjective.
     * @return the score_min.
     */
    function get_score_min()
    {
        return $this->get_default_property(self :: PROPERTY_SCORE_MIN);
    }

    /**
     * Returns the status of this Dokeos185LpIvObjective.
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Check if the lp iv objective is valid
     * @return true if the lp iv objective is valid 
     */
    function is_valid()
    {
    	$new_lp_item_view_id = $this->get_id_reference($this->get_lp_iv_id(), $this->get_database_name() . '.lp_item_view');
        
        if (! $this->get_id() || ! $new_lp_item_view_id)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'learning_path_item_view_objective', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new lp iv objective
     */
    function convert_data()
    {
    	$new_lp_item_view_id = $this->get_id_reference($this->get_lp_iv_id(), $this->get_database_name() . '.lp_item_view');
    	
    	$tracker = new WeblcmsLpiAttemptObjectiveTracker();
    	$tracker->set_lpi_view_id($new_lp_item_view_id);
    	$tracker->set_objective_id($this->get_objective_id());
    	$tracker->set_score_raw($this->get_score_raw());
    	$tracker->set_score_max($this->get_score_max());
    	$tracker->set_score_min($this->get_score_min());
    	$tracker->set_status($this->get_status());
    	$tracker->set_display_order($this->get_order_id());
    	$tracker->create();
    	
    	$this->create_id_reference($this->get_id(), $tracker->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'learning_path_item_view_objective', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $tracker->get_id())));
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