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
        
        $organisation_id = $_GET[self :: PARAM_ORGANISATION_ID];
        
        $mentor = new InternshipOrganizerMentor();
      	$mentor->set_organisation_id($organisation_id);
        
        $form = new InternshipOrganizerMentorForm(InternshipOrganizerMentorForm :: TYPE_CREATE, $mentor, $this->get_url(array(self :: PARAM_ORGANISATION_ID => $organisation_id)), $this->get_user());
       

        if ($form->validate())
        {
            $success = $form->create_mentor();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMentorCreated') : Translation :: get('InternshipOrganizerMentorNotCreated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $organisation_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_MENTORS));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_MENTORS)), Translation :: get('ViewInternshipOrganizerOrganisation')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID);
    }

}
?>