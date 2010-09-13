<?php

class InternshipOrganizerAgreementManagerReportingComponent extends InternshipOrganizerAgreementManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

    if (! InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_REPORTING, InternshipOrganizerRights :: TYPE_INTERNSHIP_ORGANIZER_COMPONENT))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
    	
    	$agreement_id = Request :: get(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID);
        $agreement= InternshipOrganizerDataManager::get_instance()->retrieve_agreement($agreement_id);
        $this->set_parameter(InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID, $agreement_id);

        $user_id = $this->get_user_id();
        $this->set_parameter(UserManager :: PARAM_USER_USER_ID, $user_id);

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('agreement general');

        $rtv = ReportingViewer :: construct($this);
        $rtv->add_template_by_name('internship_organizer_agreement_reporting_template', InternshipOrganizerManager :: APPLICATION_NAME);
        $rtv->set_breadcrumb_trail($trail);
        $rtv->hide_all_blocks();
        $rtv->run();
    }
}
?>