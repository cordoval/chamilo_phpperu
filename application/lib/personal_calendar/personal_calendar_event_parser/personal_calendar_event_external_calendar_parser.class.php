<?php
class PersonalCalendarEventExternalCalendarParser extends PersonalCalendarEventParser
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

        $calendar_events = $object->get_events();
        foreach ($calendar_events as $calendar_event)
        {
        	$object->get_repeats($calendar_event, $from_date, $to_date);
//        	if ($object->repeats($calendar_event))
//            {
//            	dump($calendar_event);
//
//            	/*$repeats = $object->get_repeats($calendar_event, $from_date, $to_date);
//            	foreach($repeats as $repeat)
//            	{
//            		$event = new PersonalCalendarEvent();
//            		$event->set_start_date($repeat[ExternalCalendar::REPEAT_START]);
//		            $event->set_end_date($repeat[ExternalCalendar::REPEAT_END]);
//		            $event->set_title($calendar_event->summary['value']);
//		            $event->set_content($calendar_event->description);
//		            $event->set_source($object->get_title());
//		            $event->set_id($publication->get_id());
//		            $events[] = $event;
//            	}*/
//            }
//            else {
//	        	$start_date = $calendar_event->dtstart['value'];
//	            $end_date = $calendar_event->dtend['value'];
//	            $event = new PersonalCalendarEvent();
//
//	            $event->set_start_date($object->get_start_date($calendar_event));
//	            $event->set_end_date($object->get_end_date($calendar_event));
//	            $event->set_title($calendar_event->summary['value']);
//
//	            //$event->set_url($this->get_parent()->get_publication_viewing_url($publication));
//
//
//	            $event->set_content($calendar_event->description);
//	            $event->set_source($object->get_title());
//	            $event->set_id($publication->get_id());
//	            $events[] = $event;
//            }
        }

        return $events;
    }
}
?>