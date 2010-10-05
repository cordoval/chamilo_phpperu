<?php
require_once Path :: get_application_path() . 'internship_organizer/php/forms/agreement_form.class.php';
require_once Path :: get_application_path() . 'internship_organizer/php/period_manager/component/viewer.class.php';

class InternshipOrganizerPeriodManagerAgreementCreatorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(self:: PARAM_USER_ID);
        
        $this->set_parameter(self :: PARAM_USER_ID, $ids);
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $id = explode('|', $ids[0]);
            $period_id = $id[0];
            
            $location_id = InternshipOrganizerRights :: get_location_id_by_identifier_from_internship_organizers_subtree($period_id, InternshipOrganizerRights :: TYPE_PERIOD);
            
            if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: ADD_AGREEMENT_RIGHT, $location_id, InternshipOrganizerRights :: TYPE_PERIOD))
            {
                $this->display_header();
                $this->display_error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $dm = InternshipOrganizerDataManager :: get_instance();
            $period = $dm->retrieve_period($period_id);
                     
            $agreement = new InternshipOrganizerAgreement();
            $agreement->set_period_id($period_id);
            $agreement->set_begin($period->get_begin());
            $agreement->set_end($period->get_end());
            $agreement->set_owner($this->get_user_id());
            
            $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_SINGLE_PERIOD_CREATE, $agreement, $this->get_url(), $ids);
            
            if ($form->validate())
            {
                $success = $form->create_single_period_agreement();
                $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementCreated') : Translation :: get('InternshipOrganizerAgreementNotCreated'), ! $success, array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoInternshipOrganizerUserSelected')));
        }
    
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PERIODS, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID))), Translation :: get('BrowseInternshipOrganizerPeriods')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => Request :: get(self :: PARAM_PERIOD_ID), DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT)), Translation :: get('ViewInternshipOrganizerPeriod')));
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID);
    }
}
?>