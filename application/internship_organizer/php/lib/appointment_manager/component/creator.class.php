<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/appointment_form.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'appointment_manager/component/browser.class.php';

class InternshipOrganizerAppointmentManagerCreatorComponent extends InternshipOrganizerAppointmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $moment_id = $_GET[self :: PARAM_MOMENT_ID];
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_APPOINTMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $moment = InternshipOrganizerDataManager::get_instance()->retrieve_moment($moment_id);
              
        $appointment = new InternshipOrganizerAppointment();
        $appointment->set_moment_id($moment_id);
        $appointment->set_owner_id($this->get_user_id());
        
        $form = new InternshipOrganizerAppointmentForm(InternshipOrganizerAppointmentForm :: TYPE_CREATE, $appointment, $this->get_url(array(self :: PARAM_MOMENT_ID => $moment_id)), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_appointment();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAppointmentCreated') : Translation :: get('InternshipOrganizerAppointmentNotCreated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_APPOINTMENT, self :: PARAM_MOMENT_ID => $moment_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAppointmentManagerBrowserComponent :: TAB_APPOINTMENTS));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_APPOINTMENT, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAppointmentManagerBrowserComponent :: TAB_APPOINTMENTS)), Translation :: get('BrowseInternshipOrganizerAgreements')));
       
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_MOMENT_ID);
    }

}
?>