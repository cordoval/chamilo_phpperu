<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/component/viewer.class.php';
require_once Path :: get_application_path() . 'lib/internship_organizer/forms/agreement_form.class.php';

class InternshipOrganizerPeriodManagerAgreementUpdaterComponent extends InternshipOrganizerPeriodManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $agreement = InternshipOrganizerDataManager::get_instance()->retrieve_agreement(Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID));
        $period = InternshipOrganizerDataManager::get_instance()->retrieve_period($agreement->get_period_id());
        
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $agreement->get_period_id())), Translation :: get('BrowseInternshipOrganizerPeriods')));
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $agreement->get_period_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT)), $period->get_name()));
        //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateInternshipOrganizerAgreement')));
        
        $form = new InternshipOrganizerAgreementForm(InternshipOrganizerAgreementForm :: TYPE_EDIT, $agreement, $this->get_url(array(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID => $agreement->get_id())), $this->get_user());
        
        if ($form->validate())
        {
            $success = $form->update_agreement();
            $this->redirect($success ? Translation :: get('InternshipOrganizerAgreementUpdated') : Translation :: get('InternshipOrganizerAgreementNotUpdated'), ! $success, array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $agreement->get_period_id(), DynamicTabsRenderer::PARAM_SELECTED_TAB => InternshipOrganizerPeriodManagerViewerComponent :: TAB_AGREEMENT));
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