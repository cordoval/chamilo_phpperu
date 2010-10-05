<?php
/**
 * $Id: subscription_user.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 *	@author Sven Vanpoucke
 */

class SubscriptionUser extends DataClass
{
    const PROPERTY_SUBSCRIPTION_ID = 'subscription_id';
    const PROPERTY_USER_ID = 'user_id';
    
    const GROUP = 1;
    const USER = 2;
    
    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_SUBSCRIPTION_ID, self :: PROPERTY_USER_ID);
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function get_subscription_id()
    {
        return $this->get_default_property(self :: PROPERTY_SUBSCRIPTION_ID);
    }

    function set_subscription_id($subscription_id)
    {
        $this->set_default_property(self :: PROPERTY_SUBSCRIPTION_ID, $subscription_id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function create()
    {
        return ReservationsDataManager :: get_instance()->create_subscription_user($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}