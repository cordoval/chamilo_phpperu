<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/mentor_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/viewer.class.php';

class InternshipOrganizerOrganisationManagerMentorEditorComponent extends InternshipOrganizerOrganisationManager
{
	
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $mentor = $this->retrieve_mentor(Request :: get(self :: PARAM_MENTOR_ID));
        $form = new InternshipOrganizerMentorForm(InternshipOrganizerMentorForm :: TYPE_EDIT, $mentor, $this->get_url(array(self :: PARAM_MENTOR_ID => $mentor->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_mentor();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMentorUpdated') : Translation :: get('InternshipOrganizerMentorNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => $mentor->get_organisation_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerViewerComponent :: TAB_MENTORS));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION,  self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID) )), Translation :: get('ViewInternshipOrganizerOrganisation')));
        
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_MENTOR_ID);
    }
}
?>