<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_form.class.php';

class InternshipOrganizerPeriodManagerAgreementEditorComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement = InternshipOrganizerDataManager :: get_instance()->retrieve_agreement(Request :: get(self :: PARAM_AGREEMENT_ID));
        
        if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_EDIT, $agreement->get_id(), InternshipOrganizerRights :: TYPE_AGREEMENT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $period_id = $agreement->get_period_id();
        
        $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_EDIT, $agreement, $this->get_url(array(self :: PARAM_AGREEMENT_ID => $agreement->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_agreement();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementUpdated') : Translation :: get('InternshipOrganizerAgreementNotUpdated'), ! $success, array(self :: PARAM_ACTION => self :: ACTION_VIEW_PERIOD, self :: PARAM_PERIOD_ID => $period_id, DynamicTabsRenderer :: PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT));
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
    
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PERIOD_ID);
    }
}
?>