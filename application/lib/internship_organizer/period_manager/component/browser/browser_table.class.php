<?php

require_once dirname(__FILE__) . '/browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser_table_cell_renderer.class.php';
/**
 * Table to display a set of learning objects.
 */
class InternshipOrganizerPeriodBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'period_browser_table';

    /**
     * Constructor
     * @see ContentObjectTable::ContentObjectTable()
     */
    function InternshipOrganizerPeriodBrowserTable($browser, $parameters, $condition)
    {
        $model = new InternshipOrganizerPeriodBrowserTableColumnModel();
        $renderer = new InternshipOrganizerPeriodBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(InternshipOrganizerPeriodManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>