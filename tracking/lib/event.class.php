<?php
/**
 * $Id: event.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 * This class presents a event
 *
 * @author Sven Vanpoucke
 */

class Event extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * Event properties
     */
    const PROPERTY_NAME = 'name';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_BLOCK = 'block';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_ACTIVE, self :: PROPERTY_BLOCK));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return TrackingDataManager :: get_instance();
    }

    /**
     * Returns the name of this Event.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Event.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the active of this Event.
     * @return the active.
     */
    function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    /**
     * Sets the active of this Event.
     * @param active
     */
    function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    /**
     * @return boolean
     */
    function is_active()
    {
        return $this->get_active() == true;
    }

    /**
     * Returns the block of this Event.
     * @return the block.
     */
    function get_block()
    {
        return $this->get_default_property(self :: PROPERTY_BLOCK);
    }

    /**
     * Sets the block of this Event.
     * @param block
     */
    function set_block($block)
    {
        $this->set_default_property(self :: PROPERTY_BLOCK, $block);
    }

    /**
     * Creates this event in the database
     */
    function create()
    {
        $trkdmg = TrackingDataManager :: get_instance();
        return $trkdmg->create_event($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    /**
     * @param string $name The name of the event
     * @param string $application The name of the application
     *
     * @return Event The event
     */
    static function factory($name, $application)
    {
        return self :: get_data_manager()->retrieve_event_by_name($name, $application);
    }

    function get_trackers()
    {
        return $this->get_data_manager()->retrieve_trackers_from_event($this->get_id());
    }

    /**
     * @deprecated
     */
    function get_tracker_registrations()
    {
        return $this->get_data_manager()->retrieve_trackers_from_event($this->get_id());
    }

    function trigger($parameters)
    {
        $adm = AdminDataManager :: get_instance();
        $setting = $adm->retrieve_setting_from_variable_name('enable_tracking', TrackingManager :: APPLICATION_NAME);

        if ($setting->get_value() != 1)
        {
            return false;
        }

        if ($this->is_active())
        {
            $tracker_registrations = $this->get_tracker_registrations();
            $data = array();

            foreach ($tracker_registrations as $tracker_registration)
            {
                $tracker_classname = $tracker_registration->get_class();
                $filename = Utilities :: camelcase_to_underscores($tracker_classname);

                $fullpath = Path :: get(SYS_PATH) . $tracker_registration->get_path() . strtolower($filename) . '.class.php';
                require_once ($fullpath);

                $parameters['event'] = $this->get_name();

                $tracker = new $tracker_classname();
                $data[] = $tracker->track($parameters);
            }

            return $data;

        }
        else
        {
            return false;
        }
    }
}

?>