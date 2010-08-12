<?php
/**
 * $Id: dokeos185_lp_view.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once Path :: get_web_application_path('weblcms') . '/trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 lp_view
 *
 * @author Sven Vanpoucke
 */
class Dokeos185LpView extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'lp_view';
    
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
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_VIEW_COUNT, self :: PROPERTY_LAST_ITEM, self :: PROPERTY_PROGRESS);
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
     * @return true if the lp view is valid 
     */
    function is_valid()
    {
        $new_lp_id = $this->get_id_reference($this->get_lp_id(), $this->get_database_name() . '.lp');
        $new_user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');
        
        if (! $this->get_id() || ! $new_lp_id || ! $new_user_id)
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'learning_path_view', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new lp view
     */
    function convert_data()
    {
        $course = $this->get_course();
        
    	$new_user_id = $this->get_id_reference($this->get_user_id(), 'main_database.user');
    	$new_course_id = $this->get_id_reference($course->get_code(), 'main_database.course');
    	$new_lp_id = $this->get_id_reference($this->get_lp_id(), $this->get_database_name() . '.lp');
    	
    	$tracker = new WeblcmsLpAttemptTracker();
    	$tracker->set_course_id($new_course_id);
    	$tracker->set_user_id($new_user_id);
    	$tracker->set_lp_id($new_lp_id);
    	$tracker->set_progress($this->get_progress());
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