<?php
class PersonalCalendarEventCalendarEventParser extends PersonalCalendarEventParser
{

    function get_events()
    {
        $events = array();
    	$publication = $this->get_publication();
        $from_date = $this->get_start_date();
        $to_date = $this->get_end_date();
        $object = $publication->get_publication_object();
        $publisher = $publication->get_publisher();
        $publishing_user = $publication->get_publication_publisher();
        
        //            if (isset($query) && $query != '')
        //            {
        //                if ((stripos($object->get_title(), $query) === false) && (stripos($object->get_description(), $query) === false))
        //                    continue;
        //            }
        

        if ($object->repeats())
        {
            $repeats = $object->get_repeats($from_date, $to_date);
            
            foreach ($repeats as $repeat)
            {
                $event = new PersonalCalendarEvent();
                $event->set_start_date($repeat->get_start_date());
                $event->set_end_date($repeat->get_end_date());
                $event->set_url($this->get_parent()->get_publication_viewing_url($publication)); 
                
                // Check whether it's a shared or regular publication
                if ($publisher != $this->get_parent()->get_user_id())
                {
                    $event->set_title($object->get_title() . ' [' . $publishing_user->get_fullname() . ']');
                }
                else
                {
                    $event->set_title($object->get_title());
                }
                
                $event->set_content($repeat->get_description());
                $event->set_source(get_class($object));
                $event->set_id($publication->get_id());
                $events[] = $event;
            }
        }
        elseif ($this->is_visible_event($object, $from_date, $to_date))
        {
            $event = new PersonalCalendarEvent();
            $event->set_start_date($object->get_start_date());
            $event->set_end_date($object->get_end_date());
            $event->set_url($this->get_publication_viewing_url($publication));
            
            // Check whether it's a shared or regular publication
            if ($publisher != $this->get_parent()->get_user_id())
            {
                $event->set_title($object->get_title() . ' [' . $publishing_user->get_fullname() . ']');
            }
            else
            {
                $event->set_title($object->get_title());
            }
            
            $event->set_content($object->get_description());
            $event->set_source(get_class($object));
            $event->set_id($publication->get_id());
            $events[] = $event;
        }
        return $events;
    }
    
    private function is_visible_event($event, $from_date, $end_date)
    {
    	return ($event->get_start_date() >= $from_date && $event->get_start_date() <= $end_date) ||
    		   ($event->get_end_date() >= $from_date && $event->get_end_date() <= $end_date) ||
    		   ($event->get_start_date() < $from_date && $event->get_end_date() > $end_date);
    }
}
?>
