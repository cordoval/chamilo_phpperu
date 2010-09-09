<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/organisation_subscribe_users_form.class.php';

class InternshipOrganizerOrganisationManagerSubscribeUsersComponent extends InternshipOrganizerOrganisationManager
{
    private $organisation;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        //$trail->add(new Breadcrumb($this->get_browse_organisations_url(), Translation :: get('BrowseInternshipOrganizerOrganisations')));
        
        $organisation_id = Request :: get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID);
        $this->organisation = $this->retrieve_organisation($organisation_id);
        
        //$trail->add(new Breadcrumb($this->get_view_organisation_url($this->organisation), $this->organisation->get_name()));
        //$trail->add(new Breadcrumb($this->get_organisation_subscribe_users_url($this->organisation), Translation :: get('AddInternshipOrganizerUsers')));
        $trail->add_help('organisation subscribe users');
        
        $form = new InternshipOrganizerOrganisationSubscribeUsersForm($this->organisation, $this->get_url(array(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => Request :: get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID))), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_organisation_rel_users();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationRelUsersCreated'), (false), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $this->organisation->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => 2));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerOrganisationRelUsersNotCreated'), (true), array(InternshipOrganizerOrganisationManager :: PARAM_ACTION => InternshipOrganizerOrganisationManager :: ACTION_VIEW_ORGANISATION, InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID => $this->organisation->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => 2));
            }
        }
        else
        {
            $this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
    }

    function get_organisation()
    {
        return $this->organisation;
    }

}
?>