<?php

require_once dirname(__FILE__) . '/publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/publication_browser_table_cell_renderer.class.php';
//require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class TestcaseSurveyPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_test_case_publication_browser_table';

    /**
     * Constructor
     */
    function TestcaseSurveyPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new TestcaseSurveyPublicationBrowserTableColumnModel();
        $renderer = new TestcaseSurveyPublicationBrowserTableCellRenderer($browser);
        $data_provider = new TestcaseSurveyPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(SurveyManager :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>