<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/mentor_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';

class InternshipOrganizerOrganisationManagerMentorCreatorComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
//        $organisation_id = Request :: get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID);
        $organisation_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
        $organisation = $this->retrieve_organisation($organisation_id);
             
        $mentor = new InternshipOrganizerMentor();
               
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
		$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $organisation->get_name()));        
//        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id), Translation :: get('BrowseInternshipOrganizerMentors'))));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipOrganizerMentor')));
        
        $form = new InternshipOrganizerMentorForm(InternshipOrganizerMentorForm :: TYPE_CREATE, $mentor, $this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation_id)), $this->get_user(), $organisation_id);
        
//        $form = new InternshipOrganizerMomentForm(InternshipOrganizerMomentForm :: TYPE_CREATE, $moment, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_mentor();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMentorCreated') : Translation :: get('InternshipOrganizerMentorNotCreated'), ! $success, array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $organisation->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_MENTORS));
        
//            $this->redirect($success ? Translation :: get('InternshipOrganizerMomentCreated') : Translation :: get('InternshipOrganizerMomentNotCreated'), ! $success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id,  DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS));
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