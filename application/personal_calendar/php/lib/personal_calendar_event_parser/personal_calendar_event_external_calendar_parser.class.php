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
            $occurences = $object->get_occurences($calendar_event, $from_date, $to_date);
            foreach ($occurences as $occurence)
            {
                $event = new PersonalCalendarEvent();
                $event->set_start_date($occurence[IcalRecurrence :: OCCURENCE_START]);

//                $end_hour = date('Hi', $occurence[IcalRecurrence :: OCCURENCE_END]);
//                if ($end_hour = '0000')
//                {
//                    $occurence[IcalRecurrence :: OCCURENCE_END]--;
//                }

                $event->set_end_date($occurence[IcalRecurrence :: OCCURENCE_END]);
                $event->set_title($calendar_event->summary['value']);
                $event->set_content($calendar_event->description);
                $event->set_source($object->get_title());
                $event->set_id($publication->get_id());
                $event->set_url($this->get_publication_viewing_url($publication, $calendar_event));
                $events[] = $event;
            }
        }

        return $events;
    }

    function get_publication_viewing_url($publication, $event)
    {
        $parameters = array();
        $parameters[PersonalCalendarManager :: PARAM_ACTION] = PersonalCalendarManager :: ACTION_VIEW_PUBLICATION;
        $parameters[PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID] = $publication->get_id();
        $parameters[Application :: PARAM_APPLICATION] = PersonalCalendarManager :: APPLICATION_NAME;
        $parameters[ExternalCalendar :: PARAM_EVENT_ID] = $event->uid['value'];

        return $this->get_parent()->get_link($parameters);
    }
}
?>