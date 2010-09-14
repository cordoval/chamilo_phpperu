<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_subscribe_user_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/agreement_viewer.class.php';

class InternshipOrganizerPeriodManagerSubscribeAgreementRelUserComponent extends InternshipOrganizerPeriodManager
{
    private $agreement;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID);
        $this->set_parameter(InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID, $agreement_id);
        $this->agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreement_id);
        $period_id = $this->agreement->get_period_id();
        
        $location_id = InternshipOrganizerRights :: get_location_id_by_identifier_from_internship_organizers_subtree($period_id, InternshipOrganizerRights :: TYPE_PERIOD);
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_USER_RIGHT, $location_id, InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        
        $user_type = Request :: get(InternshipOrganizerPeriodManager :: PARAM_USER_TYPE);
        $this->set_parameter(InternshipOrganizerPeriodManager :: PARAM_USER_TYPE, $user_type);
        
        $form = new InternshipOrganizerAgreementSubscribeUserForm($this->agreement, $this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID))), $user_type);
        
        if ($form->validate())
        {
            
            switch ($user_type)
            {
                case InternshipOrganizerUserType :: COORDINATOR :
                    $tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COORDINATOR;
                    break;
                case InternshipOrganizerUserType :: COACH :
                    $tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COACH;
                    break;
            
            }
            
            $success = $form->create_agreement_rel_user();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelUserCreated'), (false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelUserNotCreated'), (true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            }
        }
        else
        {
            $this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
    }

    function get_period()
    {
        return $this->period;
    }

}
?>