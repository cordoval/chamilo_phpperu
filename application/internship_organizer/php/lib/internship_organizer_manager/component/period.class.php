<?php
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/rel_user_browser/rel_user_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/rel_group_browser/rel_group_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/rel_category_browser/rel_category_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/browser/browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/user_browser/user_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/rel_agreement/table.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'period_manager/component/agreement_user/agreement_user_table.class.php';


class InternshipOrganizerManagerPeriodComponent extends InternshipOrganizerManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        InternshipOrganizerPeriodManager :: launch($this);
    }
}
?>