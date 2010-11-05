<?php
namespace tracking;

use common\libraries\Utilities;
use common\libraries\DataClass;

use admin\AdminDataManager;
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
        $succes = $trkdmg->create_event($this);
        if (! $succes)
        {
            return false;
        }

        $parent = TrackingRights :: get_tracking_subtree_root_id();
        if (! $parent)
        {
            TrackingRights :: create_tracking_subtree_root_location();
            $parent = TrackingRights :: get_tracking_subtree_root_id();
        }

        return TrackingRights :: create_location_in_tracking_subtree($this->get_name(), $this->get_id(), $parent);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
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
        $tracker_registrations = $this->get_data_manager()->retrieve_trackers_from_event($this->get_id());
        $trackers = array();

        foreach ($tracker_registrations as $tracker_registration)
        {
            $trackers[] = Tracker :: factory($tracker_registration->get_tracker(), $tracker_registration->get_application());
        }

        return $trackers;
    }

    /**
     * @deprecated
     */
    function get_tracker_registrations()
    {
        return $this->get_data_manager()->retrieve_trackers_from_event($this->get_id());
    }

    public static function trigger($name, $application, $parameters)
    {
        return self :: factory($name, $application)->run($parameters);
    }

    function run($parameters)
    {
        $adm = AdminDataManager :: get_instance();
        $setting = $adm->retrieve_setting_from_variable_name('enable_tracking', TrackingManager :: APPLICATION_NAME);

        if ($setting->get_value() != 1)
        {
            return false;
        }

        if ($this->is_active())
        {
            $parameters['event'] = $this->get_name();
            $data = array();

            $trackers = $this->get_trackers();
            foreach ($trackers as $tracker)
            {
                // FIXME: Temporary solution untill all trackers have been converted
                if (method_exists($tracker, 'set_event'))
                {
                    $tracker->set_event($this);
                }

                $tracker->track($parameters);

                $data[] = $tracker;
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