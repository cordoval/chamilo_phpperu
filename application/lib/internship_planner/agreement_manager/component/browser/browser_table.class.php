<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../agreement_manager.class.php';

class InternshipPlannerAgreementBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'agreement_browser_table';

    /**
     * Constructor
     */
    function InternshipPlannerAgreementBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipPlannerAgreementBrowserTableColumnModel();
        $renderer = new InternshipPlannerAgreementBrowserTableCellRenderer($browser);
        $data_provider = new InternshipPlannerAgreementBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipPlannerAgreementManager :: PARAM_DELETE_SELECTED_AGREEMENTS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>