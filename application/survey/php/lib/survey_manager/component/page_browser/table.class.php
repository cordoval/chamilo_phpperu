<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class SurveyPageBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_page_browser_table';

    /**
     * Constructor
     */
    function SurveyPageBrowserTable($browser, $parameters, $condition)
    {
        $model = new SurveyPageBrowserTableColumnModel();
        $renderer = new SurveyPageBrowserTableCellRenderer($browser);
        $data_provider = new SurveyPageBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
       
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>