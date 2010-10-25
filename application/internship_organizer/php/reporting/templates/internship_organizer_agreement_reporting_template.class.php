<?php
require_once CoreApplication :: get_application_class_lib_path('reporting'). 'reporting_template.class.php';

require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_agreement_coordinator_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_agreement_student_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_agreement_coach_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_agreement_mentor_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_agreement_add_location_reporting_block.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'reporting/blocks/internship_organizer_agreement_to_approve_reporting_block.class.php';


class InternshipOrganizerAgreementReportingTemplate extends ReportingTemplate
{

    function InternshipOrganizerAgreementReportingTemplate($parent)
    {
        parent :: __construct($parent);
        $this->add_reporting_block(new InternshipOrganizerAgreementCoordinatorReportingBlock($this));
        $this->add_reporting_block(new InternshipOrganizerAgreementCoachReportingBlock($this));
        $this->add_reporting_block(new InternshipOrganizerAgreementStudentReportingBlock($this));
        $this->add_reporting_block(new InternshipOrganizerAgreementMentorReportingBlock($this));
        $this->add_reporting_block(new InternshipOrganizerAgreementAddLocationReportingBlock($this));
        $this->add_reporting_block(new InternshipOrganizerAgreementToApproveReportingBlock($this));
            
    }

    public function display_context()
    {
    
    }

    function get_application()
    {
        return InternshipOrganizerManager :: APPLICATION_NAME;
    }

}
?>