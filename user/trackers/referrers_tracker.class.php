<?php
/**
 * $Id: referrers_tracker.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package users.lib.trackers
 */

require_once dirname(__FILE__) . '/user_tracker.class.php';

/**
 * This class tracks the referer that a user uses
 */
class ReferrersTracker extends UserTracker
{

    function validate_parameters(array $parameters = array())
    {
        $server = $parameters['server'];
        $referer = $server['HTTP_REFERER'];

        $this->set_type(self :: TYPE_REFERER);
        $this->set_name($referer);
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, self :: TYPE_REFERER);
        return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date)
    {
        $condition = new EqualityCondition(self :: PROPERTY_TYPE, self :: TYPE_REFERER);
        return $this->retrieve_tracker_items($condition);
    }
}
?>