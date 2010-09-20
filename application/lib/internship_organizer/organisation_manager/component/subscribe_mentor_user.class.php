<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/mentor_subscribe_user_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/component/mentor_viewer.class.php';

class InternshipOrganizerOrganisationManagerSubscribeMentorUserComponent extends InternshipOrganizerOrganisationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $mentor_id = Request :: get(self :: PARAM_MENTOR_ID);
        $mentor = $this->retrieve_mentor($mentor_id);
        
        $form = new InternshipOrganizerMentorSubscribeUserForm($mentor, $this->get_url(array(self :: PARAM_MENTOR_ID => Request :: get(self :: PARAM_MENTOR_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_mentor_rel_user();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerMentorRelUsersCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => $mentor->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerOrganisationManagerMentorViewerComponent :: TAB_USERS));
            }
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
        $breadcrumbtrail->add_help('mentor subscribe users');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ORGANISATION)), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_ORGANISATION, self :: PARAM_ORGANISATION_ID => Request :: get(self :: PARAM_ORGANISATION_ID))), Translation :: get('ViewInternshipOrganizerOrganisation')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_MENTOR, self :: PARAM_MENTOR_ID => Request :: get(self :: PARAM_MENTOR_ID))), Translation :: get('ViewInternshipOrganizerMentor')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_ORGANISATION_ID, self :: PARAM_MENTOR_ID);
    }

}
?>