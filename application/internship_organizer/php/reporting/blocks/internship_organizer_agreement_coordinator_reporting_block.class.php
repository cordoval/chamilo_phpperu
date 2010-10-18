<?php
require_once dirname(__FILE__) . '/../agreement_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../internship_organizer_manager/internship_organizer_manager.class.php';
class InternshipOrganizerAgreementCoordinatorReportingBlock extends InternshipOrganizerAgreementReportingBlock
{

    public function count_data()
    {
        
        //        $user_id = $this->get_user_id();
        

        $coordinator = InternshipOrganizerUserType :: COORDINATOR;
        $coordinator_count = InternshipOrganizerDataManager :: get_instance()->count_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_APPROVED, $coordinator));
        $agreements = InternshipOrganizerDataManager :: get_instance()->retrieve_agreements($this->get_condition(InternshipOrganizerAgreement :: STATUS_APPROVED, $coordinator));
        
        $categories = array();
        $nr = 0;
        while ($coordinator_count > 0)
        {
            $nr ++;
            $categories[] = $nr;
            $coordinator_count --;
        }
        
        $firstname = Translation :: get('InternshipOrganizerFirstname');
        $lastname = Translation :: get('InternshipOrganizerLastname');
        $name = Translation :: get('InternshipOrganizerAgreementName');
        $description = Translation :: get('InternshipOrganizerAgreementDescription');
        $period = Translation :: get('InternshipOrganizerPeriod');
        $begin = Translation :: get('InternshipOrganizerBegin');
        $end = Translation :: get('InternshipOrganizerEnd');
        $status = Translation :: get('InternshipOrganizerStatus');
        
        $rows = array($firstname, $lastname, $name, $description, $period, $begin, $end, $status);
        
        $reporting_data = new ReportingData();
        $reporting_data->set_categories($categories);
        $reporting_data->set_rows($rows);
        
        $nr = 0;
        while ($agreement = $agreements->next_result())
        {
            $nr ++;
            $reporting_data->add_data_category_row($nr, $firstname, $agreement->get_optional_property(User :: PROPERTY_FIRSTNAME));
            $reporting_data->add_data_category_row($nr, $lastname, $agreement->get_optional_property(User :: PROPERTY_LASTNAME));
            $reporting_data->add_data_category_row($nr, $name, $agreement->get_name());
            $reporting_data->add_data_category_row($nr, $description, strip_tags($agreement->get_description()));
            $reporting_data->add_data_category_row($nr, $period, $agreement->get_optional_property('period'));
            $reporting_data->add_data_category_row($nr, $begin, $this->get_date($agreement->get_begin()));
            $reporting_data->add_data_category_row($nr, $end, $this->get_date($agreement->get_end()));
            $reporting_data->add_data_category_row($nr, $status, $agreement->get_status());
        }
        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    function get_application()
    {
        return InternshipOrganizerManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>