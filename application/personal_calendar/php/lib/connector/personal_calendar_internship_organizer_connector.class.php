<?php

require_once (dirname(__FILE__) . '/../personal_calendar_connector.class.php');
require_once WebApplication :: get_application_class_path('internship_organizer') .  'lib/internship_organizer_data_manager.class.php';
/**
 * This personal calendar connector allows the personal calendar to retrieve the
 * published calendar events in the internship organizer application.
 */
class PersonalCalendarInternshipOrganizerConnector implements PersonalCalendarConnector
{

    /**
     * @see PersonalCalendarConnector
     */
    public function get_events($user, $from_date, $to_date)
    {
        $dm = InternshipOrganizerDataManager :: get_instance();
        $condition = $this->get_moment_condition($user);
        
        $moments = $dm->retrieve_moments($condition);
        
        $result = array();
        while ($moment = $moments->next_result())
        {
            
            if ($this->is_visible_event($moment, $from_date, $to_date))
            {
                $event = new PersonalCalendarEvent();
                $event->set_start_date($moment->get_begin());
                $event->set_end_date($moment->get_end());
                $event->set_url('run.php?application=internship_organizer&amp;go=agreement&amp;action=moment_viewer&amp;moment_id=' . $moment->get_id());
                $event->set_title($moment->get_name());
                $event->set_content($moment->get_description());
                $event->set_source('internship_organizer_moment');
                $event->set_id($moment->get_id());
                $result[] = $event;
            }
        }
        
        $condition = $this->get_appointment_condition($user);
        $moments = $dm->retrieve_moment_rel_appointments($condition);
        
        while ($moment = $moments->next_result())
        {
            
            if ($this->is_visible_event($moment, $from_date, $to_date))
            {
                $event = new PersonalCalendarEvent();
                $event->set_start_date($moment->get_begin());
                $event->set_end_date($moment->get_end());
                $event->set_url('run.php?application=internship_organizer&amp;go=agreement&amp;action=moment_viewer&amp;moment_id=' . $moment->get_id());
                $event->set_title($moment->get_name());
                $event->set_content($moment->get_description());
                $event->set_source('internship_organizer_moment');
                $event->set_id($moment->get_id());
                $result[] = $event;
            }
        }
        
        return $result;
    }

    private function is_visible_event($event, $from_date, $end_date)
    {
        return ($event->get_begin() >= $from_date && $event->get_begin() <= $end_date) || ($event->get_end() >= $from_date && $event->get_end() <= $end_date) || ($event->get_begin() < $from_date && $event->get_end() > $end_date);
    }

    function get_moment_condition($user)
    {
        $dm = InternshipOrganizerDataManager :: get_instance();
        $moment_alias = $dm->get_alias(InternshipOrganizerMoment :: get_table_name());
        
        $user_id = $user->get_id();
        
        $conditions = array();
        $condition = new EqualityCondition(InternshipOrganizerMoment :: PROPERTY_OWNER, $user_id, $moment_alias, true);
        return $condition;
    }

    function get_appointment_condition($user)
    {
        $dm = InternshipOrganizerDataManager :: get_instance();
        $appointment_alias = $dm->get_alias(InternshipOrganizerAppointment :: get_table_name());
        
        $user_id = $user->get_id();
        
        $conditions = array();
        $condition = new EqualityCondition(InternshipOrganizerAppointment :: PROPERTY_OWNER_ID, $user_id, $appointment_alias, true);
        return $condition;
    }

}
?>