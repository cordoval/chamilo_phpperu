<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_subscribe_user_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/browser.class.php';


class InternshipOrganizerPeriodManagerSubscribeAgreementRelUserComponent extends InternshipOrganizerPeriodManager
{
    private $agreement;
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        
//        $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
//        $trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));
        
        $agreement_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID);
             
        $this->agreement = InternshipOrganizerDataManager::get_instance()->retrieve_agreement($agreement_id);
        
//        $user_type = Request :: get(InternshipOrganizerPeriodManager :: PARAM_USER_TYPE);
        
        $user_type = InternshipOrganizerUserType::COACH;
        
//        $trail->add(new Breadcrumb($this->get_period_subscribe_category_url($this->period), Translation :: get('AddInternshipOrganizerCategories')));
//        $trail->add_help('period subscribe category');
        
        $form = new InternshipOrganizerAgreementSubscribeUserForm($this->agreement, $this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID))), $this->get_user());
        
        if ($form->validate())
        {
            
        	switch ($user_type) {
            	case InternshipOrganizerUserType:: COORDINATOR  :
            	$tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COORDINATOR;
            	break;
            	case InternshipOrganizerUserType:: COACH  :
            	$tab = InternshipOrganizerPeriodManagerAgreementViewerComponent :: TAB_COACH;
            	break;
            	
            }
        	
        	
        	$success = $form->create_categroy_rel_period();
            if ($success)
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelUserCreated'), (false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => $tab));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerAgreementRelUserNotCreated'), (true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_AGREEMENT, InternshipOrganizerPeriodManager :: PARAM_AGREEMENT_ID => $this->agreement->get_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => $tab));
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