<?php

class InternshipOrganizerAgreementManagerReportingComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $agreement_id = Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
        $agreement= InternshipOrganizerDataManager::get_instance()->retrieve_agreement($agreement_id);
        $this->set_parameter(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID, $agreement_id);

        $user_id = $this->get_user_id();
        $this->set_parameter(UserManager :: PARAM_USER_USER_ID, $user_id);


        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(InternshipOrganizerManager :: PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER)), Translation :: get('InternshipOrganizer')));
        //$trail->add(new Breadcrumb($this->get_browse_agreements_url(), Translation :: get('BrowseInternshipOrganizerAgreements')));
        $trail->add_help('agreement general');

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('internship_organizer_agreement_reporting_template', InternshipOrganizerManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->hide_all_blocks();
        $rtv->run();
    }
}
?>