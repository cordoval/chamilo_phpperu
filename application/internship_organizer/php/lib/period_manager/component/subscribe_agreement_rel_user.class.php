<?php

require_once Path :: get_application_path() . 'internship_organizer/php/forms/agreement_subscribe_user_form.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/period_manager/component/agreement_viewer.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/user_type.class.php';

class InternshipOrganizerPeriodManagerSubscribeAgreementRelUserComponent extends InternshipOrganizerPeriodManager
{
    private $agreement;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement_id = Request :: get(self :: PARAM_AGREEMENT_ID);
        $this->set_parameter(self :: PARAM_AGREEMENT_ID, $agreement_id);
        $this->agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement($agreement_id);
        $period_id = $this->agreement->get_period_id();
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: SUBSCRIBE_USER_RIGHT, $period_id, InternshipOrganizerRights :: TYPE_PERIOD))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $user_type = Request :: get(self :: PARAM_USER_TYPE);
        $this->set_parameter(self :: PARAM_USER_TYPE, $user_type);
        
        $form = new InternshipOrganizerAgreementSubscribeUserForm($this->agreement, $this->get_url(array(self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), $user_type);
        
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
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelUserCreated'), (false), array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelUserNotCreated'), (true), array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
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
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT)), Translation :: get('ViewInternshipOrganizerPeriod')));
        $user_type = Request :: get(self :: PARAM_USER_TYPE);
        if($user_type == InternshipOrganizerUserType::COACH){
        	$tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COACH;
        }else{
        	$tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COORDINATOR;
        }
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_AGREEMENT, self :: PARAM_AGREEMENT_ID => Request :: get(self :: PARAM_AGREEMENT_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab)), Translation :: get('ViewInternshipOrganizerPeriod')));
        
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID, self :: PARAM_AGREEMENT_ID);
    }

}
?>