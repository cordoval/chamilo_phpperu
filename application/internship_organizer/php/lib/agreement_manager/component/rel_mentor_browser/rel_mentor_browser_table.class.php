<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/rel_mentor_browser/rel_mentor_browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/rel_mentor_browser/rel_mentor_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/rel_mentor_browser/rel_mentor_browser_table_cell_renderer.class.php';

class InternshipOrganizerAgreementRelMentorBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'rel_mentor_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerAgreementRelMentorBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerAgreementRelMentorBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerAgreementRelMentorBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerAgreementRelMentorBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerAgreementRelMentorBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    
    }
}
?>