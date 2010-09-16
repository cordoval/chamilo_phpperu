<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/internship_organizer_manager/internship_organizer_manager.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/moment_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/component/viewer.class.php';

class InternshipOrganizerAgreementManagerMomentCreatorComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_MOMENT_RIGHT, $agreement_id, InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $agreement = $this->retrieve_agreement($agreement_id);
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $moment = new InternshipOrganizerMoment();
        $moment->set_agreement_id($agreement_id);
        $moment->set_owner($this->get_user_id());
        
        $form = new InternshipOrganizerMomentForm(InternshipOrganizerMomentForm :: TYPE_CREATE, $moment, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id)), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->create_moment();
            $this->redirect($success ? Translation :: get('InternshipOrganizerMomentCreated') : Translation :: get('InternshipOrganizerMomentNotCreated'), ! $success, array(InternshipOrganizerAgreementManager :: PARAM_ACTION => InternshipOrganizerAgreementManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerAgreementManagerViewerComponent :: TAB_MOMENTS));
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