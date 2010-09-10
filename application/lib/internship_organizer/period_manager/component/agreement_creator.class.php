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
        if (isset($ids))
        {
            $ids = Request :: get(InternshipOrganizerPeriodManager :: PARAM_USER_ID);
        }
        else
        {
            $ids = unserialize(Request :: post(InternshipOrganizerPeriodManager :: PARAM_USER_ID));
        }
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $period_id;
            
            $id = explode('|', $ids[0]);
            $period_id = $id[0];
            
            $dm = InternshipOrganizerDataManager :: get_instance();
            $period = $dm->retrieve_period($period_id);
            
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('period general');
            
            $agreement = new InternshipOrganizerAgreement();
            $agreement->set_period_id($period_id);
            $agreement->set_begin($period->get_begin());
            $agreement->set_end($period->get_end());
            
            $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_SINGLE_PERIOD_CREATE, $agreement, $this->get_url(), $ids);
            
            if ($form->validate())
            {
                $success = $form->create_single_period_agreement();
                $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementCreated') : Translation :: get('InternshipOrganizerAgreementNotCreated'), ! $success, array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT));
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