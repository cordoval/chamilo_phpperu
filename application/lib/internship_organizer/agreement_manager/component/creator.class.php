<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_form.class.php';

class InternshipOrganizerAgreementManagerCreatorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_ADD, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    
        $agreement = new InternshipOrganizerAgreement();
        $agreement->set_owner($this->get_user_id());
        
        $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_CREATE, $agreement, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_agreement();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementCreated') : Translation :: get('InternshipOrganizerAgreementNotCreated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT)), Translation :: get('BrowseInternshipOrganizerAgreements')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_AGREEMENT_ID);
    }
}
?>