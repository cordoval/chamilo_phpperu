<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../agreement_manager.class.php';

class InternshipOrganizerMomentBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'moment_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerMomentBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerMomentBrowserTableColumnModel();
        $renderer = new InternshipOrganizerMomentBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerMomentBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerAgreementManager :: PARAM_DELETE_SELECTED_MOMENTS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>