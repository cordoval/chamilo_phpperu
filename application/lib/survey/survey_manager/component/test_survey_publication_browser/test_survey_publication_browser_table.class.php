<?php
/**
 * $Id: survey_publication_browser_table.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_publication_browser
 */
require_once dirname(__FILE__) . '/test_survey_publication_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/test_survey_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/test_survey_publication_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

/**
 * Table to display a list of survey_publications
 *
 * @author Sven Vanpoucke
 * @author 
 */
class TestSurveyPublicationBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_publication_browser_table';

    /**
     * Constructor
     */
    function TestSurveyPublicationBrowserTable($browser, $parameters, $condition)
    {
        $model = new TestSurveyPublicationBrowserTableColumnModel();
        $renderer = new TestSurveyPublicationBrowserTableCellRenderer($browser);
        $data_provider = new TestSurveyPublicationBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(SurveyManager :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS, Translation :: get('RemoveSelected'));
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>