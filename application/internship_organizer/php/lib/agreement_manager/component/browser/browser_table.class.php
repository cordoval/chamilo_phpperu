<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/browser/browser_table_data_provider.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/browser/browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/component/browser/browser_table_cell_renderer.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'agreement_manager/agreement_manager.class.php';

class InternshipOrganizerAgreementBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'agreement_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerAgreementBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerAgreementBrowserTableColumnModel();
        $renderer = new InternshipOrganizerAgreementBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerAgreementBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerAgreementManager :: PARAM_DELETE_SELECTED_AGREEMENTS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>