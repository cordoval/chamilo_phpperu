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
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
   
        $agreement = new InternshipOrganizerAgreement();
        $agreement->set_owner($this->get_user_id());
        
        $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_CREATE, $agreement, $this->get_url());
        
        if ($form->validate())
        {
            $success = $form->create_agreement();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementCreated') : Translation :: get('InternshipOrganizerAgreementNotCreated'), ! $success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_BROWSE_AGREEMENT));
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