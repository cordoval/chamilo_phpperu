<?php

class InternshipOrganizerAppointmentManager extends SubManager
{
    
    const PARAM_ACTION = 'action';
    const PARAM_APPOINTMENT_ID = 'appointment_id';
    const PARAM_MOMENT_ID = 'moment_id';
       
    const ACTION_CREATE_APPOINTMENT = 'creator';
    const ACTION_BROWSE_APPOINTMENT = 'browser';
    const ACTION_EDIT_APPOINTMENT = 'editor';
    const ACTION_DELETE_APPOINTMENT = 'deleter';
    const ACTION_VIEW_APPOINTMENT = 'viewer';
    const ACTION_PUBLISH_APPOINTMENT = 'publisher';
        
   	const ACTION_VIEW_MOMENT = 'moment_viewer';
   
    const ACTION_REPORTING = 'reporting';
       

    const DEFAULT_ACTION = self :: ACTION_BROWSE_APPOINTMENT;

    function InternshipOrganizerAppointmentManager($manager)
    {
        parent :: __construct($manager);
    }

    function get_application_component_path()
    {
        return Path :: get_application_path() . 'internship_organizer/php/lib/appointment_manager/component/';
    }

  
    //url creation
    function get_create_appointment_url($moment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_APPOINTMENT, self :: PARAM_MOMENT_ID => $moment->get_optional_property('moment_id')));
    }

    function get_update_appointment_url($appointment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_APPOINTMENT, self :: PARAM_APPOINTMENT_ID => $appointment->get_id()));
    }

    function get_delete_appointment_url($appointment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_APPOINTMENT, self :: PARAM_APPOINTMENT_ID => $appointment->get_id()));
    }

    function get_browse_appointments_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_APPOINTMENT));
    }
   
    function get_browse_moments_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MOMENTS));
    }

    function get_view_moment_url($appointment)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MOMENT, self :: PARAM_APPOINTMENT_ID => $appointment->get_id()));
    }
  

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        parent :: launch(__CLASS__, $application);
    }
}

?>