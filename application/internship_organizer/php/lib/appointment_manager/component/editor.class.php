<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/appointment_form.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'moment_manager/component/viewer.class.php';


class InternshipOrganizerAppointmentManagerAppointmentEditorComponent extends InternshipOrganizerAppointmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $appointment_id = Request :: get(self :: PARAM_APPOINTMENT_ID);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $appointment_id, InternshipOrganizerRights :: TYPE_APPOINTMENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $appointment = $this->retrieve_appointment($appointment_id);
        
        $form = new InternshipOrganizerAppointmentForm(InternshipOrganizerAppointmentForm :: TYPE_EDIT, $appointment, $this->get_url(array(self :: PARAM_APPOINTMENT_ID => $appointment->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_appointment();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAppointmentUpdated') : Translation :: get('InternshipOrganizerAppointmentNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_MOMENT, self :: PARAM_MOMENT_ID => $appointment->get_moment_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAppointmentManagerViewerComponent :: TAB_APPOINTMENTS));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_MOMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $moment_id = Request :: get(self :: PARAM_MOMENT_ID);
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MOMENT, self :: PARAM_MOMENT_ID => $moment_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAppointmentManagerViewerComponent :: TAB_APPOINTMENTS)), Translation :: get('ViewInternshipOrganizerAgreement')));
        
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_MOMENT_ID, self :: PARAM_APPOINTMENT_ID);
    }

}
?>