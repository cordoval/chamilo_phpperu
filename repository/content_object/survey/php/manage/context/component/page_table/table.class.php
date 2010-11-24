<?php namespace repository\content_object\survey;

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';

class SurveyPageTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_page_browser_table';

    /**
     * Constructor
     */
    function __construct($browser, $parameters, $condition)
    {
        $model = new SurveyPageTableColumnModel();
        $renderer = new SurveyPageTableCellRenderer($browser);
        $data_provider = new SurveyPageTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
       
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>