<?php

require_once dirname(__FILE__) . '/question_browser_table_data_provider.class.php';
require_once dirname(__FILE__) . '/question_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/question_browser_table_cell_renderer.class.php';

class SurveyPageQuestionBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_page_question_browser_table';

   
    function SurveyPageQuestionBrowserTable($browser, $parameters, $condition)
    {
        
    
    	$model = new SurveyPageQuestionBrowserTableColumnModel();
        $renderer = new SurveyPageQuestionBrowserTableCellRenderer($browser);
        $data_provider = new SurveyPageQuestionBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, SurveyPageQuestionBrowserTable :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
              
        $actions = array();
        
        $actions[] = new ObjectTableFormAction(SurveyBuilder :: PARAM_UNSUBSCRIBE_SELECTED, Translation :: get('UnsubscribeSelected'), false);
        
        $this->set_form_actions($actions);
    }
 
}
?>