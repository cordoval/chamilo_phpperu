<?php
/**
 * $Id: dokeos185_lp_item_view.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once Path :: get_web_application_path('weblcms') . '/trackers/weblcms_lpi_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 lp_item_view
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpItemView extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'lp_item_view';
    
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
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_ITEM_ID, self :: PROPERTY_LP_VIEW_ID, self :: PROPERTY_VIEW_COUNT, self :: PROPERTY_START_TIME, self :: PROPERTY_TOTAL_TIME, self :: PROPERTY_SCORE, self :: PROPERTY_STATUS, self :: PROPERTY_SUSPEND_DATA, self :: PROPERTY_LESSON_LOCATION, self :: PROPERTY_CORE_EXIT, self :: PROPERTY_MAX_SCORE);
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
     * @return true if the lp item view is valid 
     */
    function is_valid()
    {
        $new_lp_item_id = $this->get_id_reference($this->get_lp_item_id(), $this->get_database_name() . '.lp_item');
        $new_lp_view_id = $this->get_id_reference($this->get_lp_view_id(), $this->get_database_name() . '.lp_view');
        
        if (! $this->get_id() || ! $new_lp_item_id || ! $new_lp_view_id)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'learning_path_item_view', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new lp item view
     */
    function convert_data()
    {
        $new_lp_item_id = $this->get_id_reference($this->get_lp_item_id(), $this->get_database_name() . '.lp_item');
        $new_lp_view_id = $this->get_id_reference($this->get_lp_view_id(), $this->get_database_name() . '.lp_view');
    	
    	$tracker = new WeblcmsLpiAttemptTracker();
    	$tracker->set_lp_item_id($new_lp_item_id);
    	$tracker->set_lp_view_id($new_lp_view_id);
    	$tracker->set_start_time($this->get_start_time());
    	$tracker->set_total_time($this->get_total_time());
    	$tracker->set_score($this->get_score());
    	$tracker->set_status($this->get_status());
    	$tracker->set_lesson_location($this->get_lesson_location());
    	$tracker->set_suspend_data($this->get_suspend_data());
    	$tracker->set_min_score(0);
    	$tracker->set_max_score($this->get_max_score());
    	$tracker->create();
    	
    	$this->create_id_reference($this->get_id(), $tracker->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'learning_path_view', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $tracker->get_id())));
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