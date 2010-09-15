<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_form.class.php';

class InternshipOrganizerAgreementManagerUpdaterComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $agreement = $this->retrieve_agreement(Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID));
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: EDIT_AGREEMENT_RIGHT, $agreement, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_EDIT, $agreement, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_agreement();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementUpdated') : Translation :: get('InternshipOrganizerAgreementNotUpdated'), ! $success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT));
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