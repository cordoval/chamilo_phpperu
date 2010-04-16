<?php

require_once dirname(__FILE__) . '/table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/survey_question_table/default_survey_question_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

class SurveyQuestionBrowserTableCellRenderer extends DefaultSurveyQuestionTableCellRenderer
{
    /**
     * The browser component
     * @var SurveyManagerSurveyQuestionsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function SurveyQuestionBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_question)
    {
        if ($column === SurveyQuestionBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_question);
        }
        
        return parent :: render_cell($column, $survey_question);
    }

    /**
     * Gets the action links to display
     * @param SurveyQuestion $survey_question The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_question)
    {
        $toolbar_data = array();
        $toolbar_data[] = array('href' => $this->browser->get_question_reporting_url($survey_question), 'label' => Translation :: get('VieuwQuestionResults'), 'img' => Theme :: get_common_image_path() . 'action_view_results.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>