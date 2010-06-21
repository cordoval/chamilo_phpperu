<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/mentor_form.class.php';

class InternshipOrganizerOrganisationManagerUpdaterComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_MENTOR)), Translation :: get('BrowseInternshipOrganizerMentors')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateInternshipOrganizerMentor')));
        
        $mentor = $this->retrieve_mentor(Request :: get(InternshipOrganizerMentorManager :: PARAM_MENTOR_ID));
        $form = new InternshipOrganizerMentorForm(InternshipOrganizerMentorForm :: TYPE_EDIT, $mentor, $this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_MENTOR_ID => $mentor->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_mentor();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMentorUpdated') : Translation :: get('InternshipOrganizerMentorNotUpdated'), ! $success, array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_MENTOR));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>