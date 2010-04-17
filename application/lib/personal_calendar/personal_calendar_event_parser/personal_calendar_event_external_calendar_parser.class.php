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
        
        if (IcalRecurrence :: DEBUG)
        {
            
            echo '<hr />';
            echo '<b>GENERAL PARAMETERS</b>';
            echo '<hr />';
            echo '<table class="data_table">';
            echo '<thead>';
            echo '<tr><th>Variable</th><th></th><th></th>';
            echo '</thead>';
            echo '<tbody>';
            echo '<tr><td>$this->mArray_start</td><td>' . $from_date . '</td>';
            echo '<td>' . date('r', $from_date) . '</td></tr>';
            echo '<tr><td>$this->mArray_end</td><td>' . $to_date . '</td>';
            echo '<td>' . date('r', $to_date) . '</td></tr>';
            echo '</tbody>';
            echo '</table>';
        }
        
        $calendar_events = $object->get_events();
        foreach ($calendar_events as $calendar_event)
        {
            if (IcalRecurrence :: DEBUG)
            {
                echo '<hr />';
                echo '<b>ICAL RECURRENCE</b>';
                echo '<hr />';
                echo '<table class="data_table">';
                echo '<thead>';
                echo '<tr><th>Variable</th><th></th><th></th>';
                echo '</thead>';
                echo '<tbody>';
            }
            $occurences = $object->get_occurences($calendar_event, $from_date, $to_date);
            
            if (IcalRecurrence :: DEBUG)
            {
                echo '</tbody>';
                echo '</table>';
                echo '<br /><br /><br /><br />';
            }
            
            foreach ($occurences as $occurence)
            {
                $event = new PersonalCalendarEvent();
                
                $event->set_start_date($occurence['start']);
                $event->set_end_date($occurence['end']);
                $event->set_title($calendar_event->summary['value']);
                
                //$event->set_url($this->get_parent()->get_publication_viewing_url($publication));
                

                $event->set_content($calendar_event->description);
                $event->set_source($object->get_title());
                $event->set_id($publication->get_id());
                $events[] = $event;
            }
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