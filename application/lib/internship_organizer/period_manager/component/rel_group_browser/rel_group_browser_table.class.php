<?php

require_once dirname(__FILE__) . '/rel_group_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_group_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_group_browser_table_cell_renderer.class.php';

class InternshipOrganizerPeriodGroupBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'rel_group_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerPeriodGroupBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerPeriodGroupBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerPeriodGroupBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerPeriodGroupBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerPeriodGroupBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    
    }
}
?>