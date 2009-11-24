<?php
/**
 * $Id: tracker_setting.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 * This class presents a tracker_setting
 *
 * @author Sven Vanpoucke
 */


class TrackerSetting extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * TrackerSetting properties
     */
    const PROPERTY_TRACKER_ID = 'tracker_id';
    const PROPERTY_SETTING = 'setting';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TRACKER_ID, self :: PROPERTY_SETTING, self :: PROPERTY_VALUE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return TrackingDataManager :: get_instance();
    }

    /**
     * Returns the tracker_id of this TrackerSetting.
     * @return the tracker_id.
     */
    function get_tracker_id()
    {
        return $this->get_default_property(self :: PROPERTY_TRACKER_ID);
    }

    /**
     * Sets the tracker_id of this TrackerSetting.
     * @param tracker_id
     */
    function set_tracker_id($tracker_id)
    {
        $this->set_default_property(self :: PROPERTY_TRACKER_ID, $tracker_id);
    }

    /**
     * Returns the setting of this TrackerSetting.
     * @return the setting.
     */
    function get_setting()
    {
        return $this->get_default_property(self :: PROPERTY_SETTING);
    }

    /**
     * Sets the setting of this TrackerSetting.
     * @param setting
     */
    function set_setting($setting)
    {
        $this->set_default_property(self :: PROPERTY_SETTING, $setting);
    }

    /**
     * Returns the value of this TrackerSetting.
     * @return the value.
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the value of this TrackerSetting.
     * @param value
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * Creates this event in the database
     */
    function create()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        $trkdmg->create_tracker_setting($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>