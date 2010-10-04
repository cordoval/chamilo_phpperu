<?php
/**
 * $Id: events.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib
 */

/**
 * Class to create and trigger tracker events
 * @author Sven Vanpoucke
 */
class Events
{

    /** 
     * Create an event
     * @param String $event_name the event name (must be a unique name)
     */
    public static function create_event($event_name, $block)
    {
        $event = new Event();
        $event->set_name($event_name);
        $event->set_active(true);
        $event->set_block($block);
        if (! $event->create())
        {
            return false;
        }
        
        return $event;
    }
}
?>