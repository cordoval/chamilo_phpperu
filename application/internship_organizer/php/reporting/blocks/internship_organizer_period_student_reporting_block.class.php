<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Translation;

use user\UserDataManager;

require_once dirname(__FILE__) . '/../period_reporting_block.class.php';
require_once WebApplication :: get_application_path('internship_organizer') . 'php/lib/internship_organizer_manager/internship_organizer_manager.class.php';

class InternshipOrganizerPeriodStudentReportingBlock extends InternshipOrganizerPeriodReportingBlock
{

    public function count_data()
    {
        
        $period_id = $this->get_period_id();
        $period = InternshipOrganizerDataManager :: get_instance()->retrieve_period($period_id);
        
        $student = InternshipOrganizerUserType :: STUDENT;
        $student_count = count($period->get_user_ids($student));
        
        $categories = array();
        $nr = 0;
        while ($student_count > 0)
        {
            $nr++;
            $categories[] = $nr;
            $student_count --;
         }
        
        $firstname = Translation :: get('InternshipOrganizerFirstname');
        $lastname = Translation :: get('InternshipOrganizerLastname');
        $email = Translation :: get('InternshipOrganizerEmail');
        $rows = array($firstname, $lastname, $email);
        
        $reporting_data = new ReportingData();
        $reporting_data->set_categories($categories);
        $reporting_data->set_rows($rows);
        
        $user_ids = $period->get_user_ids($student);
              
        $nr = 0;
        foreach ($user_ids as $user_id)
        {
            $nr++;
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            $reporting_data->add_data_category_row($nr, $firstname, $user->get_firstname());
            $reporting_data->add_data_category_row($nr, $lastname, $user->get_lastname());
            $reporting_data->add_data_category_row($nr, $email, $user->get_email());
            
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