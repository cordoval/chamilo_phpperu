<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * $Id: quota.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class Quota extends DataClass
{
    const PROPERTY_CREDITS = 'credits';
    const PROPERTY_TIME_UNIT = 'time_unit';
    
    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CREDITS, self :: PROPERTY_TIME_UNIT));
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function get_credits()
    {
        return $this->get_default_property(self :: PROPERTY_CREDITS);
    }

    function set_credits($credits)
    {
        $this->set_default_property(self :: PROPERTY_CREDITS, $credits);
    }

    function get_time_unit()
    {
        return $this->get_default_property(self :: PROPERTY_TIME_UNIT);
    }

    function set_time_unit($time_unit)
    {
        $this->set_default_property(self :: PROPERTY_TIME_UNIT, $time_unit);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}