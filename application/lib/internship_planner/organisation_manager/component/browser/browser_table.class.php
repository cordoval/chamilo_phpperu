<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../organisation_manager.class.php';

class InternshipPlannerOrganisationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'organisation_browser_table';

    /**
     * Constructor
     */
    function InternshipPlannerOrganisationBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipPlannerOrganisationBrowserTableColumnModel();
        $renderer = new InternshipPlannerOrganisationBrowserTableCellRenderer($browser);
        $data_provider = new InternshipPlannerOrganisationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipPlannerOrganisationManager :: PARAM_DELETE_SELECTED_ORGANISATIONS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>