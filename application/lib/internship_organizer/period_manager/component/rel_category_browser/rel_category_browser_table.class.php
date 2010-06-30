<?php

require_once dirname(__FILE__) . '/rel_category_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/rel_category_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/rel_category_browser_table_cell_renderer.class.php';

class InternshipOrganizerCategoryRelPeriodBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'rel_category_browser_table';

    /**
     * Constructor
     */
    function InternshipOrganizerCategoryRelPeriodBrowserTable($browser, $parameters, $condition)
    {
        
        $model = new InternshipOrganizerCategoryRelPeriodBrowserTableColumnModel($browser);
        $renderer = new InternshipOrganizerCategoryRelPeriodBrowserTableCellRenderer($browser);
        $data_provider = new InternshipOrganizerCategoryRelPeriodBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, InternshipOrganizerCategoryRelPeriodBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $actions = array();
        $this->set_additional_parameters($parameters);
        $this->set_form_actions($actions);
    
    }
}
?>