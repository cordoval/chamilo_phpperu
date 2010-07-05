<?php
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_form.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/viewer.class.php';

class InternshipOrganizerPeriodManagerAgreementCreatorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(InternshipOrganizerPeriodManager :: PARAM_USER_ID);
              
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $user_ids = array();
            $period_id;
            
            foreach ($ids as $id)
            {
                $id = explode('|', $id);
                $period_id = $id[0];
                $user_ids[] = $id[1];
            }
            
            $period = $this->retrieve_period($period_id);
                      
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
            $trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period_id)), Translation :: get('BrowseInternshipOrganizerPeriods')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateInternshipOrganizerAgreement')));
            $trail->add_help('period general');
            
            $agreement = new InternshipOrganizerAgreement();
            $agreement->set_period_id($period_id);
            $agreement->set_begin($period->get_begin());
            $agreement->set_end($period->get_end());
                        
            $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_CREATE, $agreement, $this->get_url(), $user_ids);
            
            if ($form->validate())
            {
                $success = $form->create_agreement();
                $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementCreated') : Translation :: get('InternshipOrganizerAgreementNotCreated'), ! $success, array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_STUDENT));
            }
            else
            {
                $this->display_header($trail);
                $form->display();
                $this->display_footer();
            }
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerUserSelected')));
        }
    
    }
}
?>