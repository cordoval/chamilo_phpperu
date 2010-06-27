<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/mentor_form.class.php';

class InternshipOrganizerOrganisationManagerMentorCreatorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $organisation_id = Request :: get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID);
             
        $mentor = new InternshipOrganizerMentor();
               
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id), Translation :: get('BrowseInternshipOrganizerMentors'))));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipOrganizerMentor')));
        
        $form = new InternshipOrganizerMentorForm(InternshipOrganizerMentorForm :: TYPE_CREATE, $mentor, $this->get_url(), $this->get_user(), $organisation_id);
        
        if ($form->validate())
        {
            $success = $form->create_mentor();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMentorCreated') : Translation :: get('InternshipOrganizerMentorNotCreated'), ! $success, array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id, DynamicTabsRenderer::PARAM_SELECTED_TAB => 1));
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