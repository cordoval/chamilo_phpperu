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
        $this->set_id($trkdmg->get_next_id(self :: get_table_name()));
        return $trkdmg->create_event($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>