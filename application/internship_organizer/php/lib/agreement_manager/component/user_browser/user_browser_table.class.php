<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/user_browser/user_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/user_browser/user_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/user_browser/user_browser_table_cell_renderer.class.php';

class InternshipOrganizerAgreementUserBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'user_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerAgreementUserBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerAgreementUserBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerAgreementUserBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerAgreementUserBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerAgreementUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    
    }
}
?>