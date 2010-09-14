<?php

require_once (dirname(__FILE__) . '/../personal_calendar_connector.class.php');
require_once (dirname(__FILE__) . '/../../internship_organizer/internship_organizer_data_manager.class.php');
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
        $condition = $this->get_conditions($user);

        $moments = $dm->retrieve_moment_rel_users($condition);
        $result = array();
        while ($moment = $moments->next_result())
        {
            
//        	dump($moment);
        	
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
    	return ($event->get_begin() >= $from_date && $event->get_begin() <= $end_date) ||
    		   ($event->get_end() >= $from_date && $event->get_end() <= $end_date) ||
    		   ($event->get_begin() < $from_date && $event->get_end() > $end_date);
    }

    function get_conditions($user)
    {
        $dm = InternshipOrganizerDataManager :: get_instance();
        
        $user_id = $user->get_id();
        
        $conditions = array();
        
        $condition = new EqualityCondition(InternshipOrganizerAgreementRelUser :: PROPERTY_USER_ID, $user_id, InternshipOrganizerAgreementRelUser :: get_table_name());
       	
           
        return $condition;
    }
}
?>