<?php

require_once require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';
require_once require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'forms/agreement_form.class.php';

class InternshipOrganizerAgreementManagerEditorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $agreement = $this->retrieve_agreement(Request :: get(self :: PARAM_AGREEMENT_ID));
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_EDIT, $agreement, $this->get_url(array(self :: PARAM_AGREEMENT_ID => $agreement->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_agreement();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementUpdated') : Translation :: get('InternshipOrganizerAgreementNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE_AGREEMENT));
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