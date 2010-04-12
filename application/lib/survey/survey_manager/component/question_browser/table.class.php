<?php

require_once dirname(__FILE__) . '/table_data_provider.class.php';
require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class SurveyQuestionBrowserTable extends ObjectTable
{
    const DEFAULT_NAME = 'survey_question_browser_table';

    /**
     * Constructor
     */
    function SurveyQuestionBrowserTable($browser, $parameters, $condition)
    {
        $model = new SurveyQuestionBrowserTableColumnModel();
        $renderer = new SurveyQuestionBrowserTableCellRenderer($browser);
        $data_provider = new SurveyQuestionBrowserTableDataProvider($browser, $condition);
        parent :: __construct($data_provider, self :: DEFAULT_NAME, $model, $renderer);
        $this->set_additional_parameters($parameters);
        $actions = array();
        
//        $actions[] = new ObjectTableFormAction(SurveyManager :: PARAM_DELETE_SELECTED_SURVEY_PUBLICATIONS, Translation :: get('RemoveSelected'));
//        $actions[] = new ObjectTableFormAction(SurveyManager :: PARAM_MAIL_PARTICIPANTS, Translation :: get('InviteParticipants'));
        
        
        $this->set_form_actions($actions);
        $this->set_default_row_count(20);
    }
}
?>