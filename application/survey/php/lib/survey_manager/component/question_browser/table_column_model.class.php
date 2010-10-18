<?php

require_once dirname(__FILE__) . '/../../../tables/survey_question_table/default_survey_question_table_column_model.class.php';

class SurveyQuestionBrowserTableColumnModel extends DefaultSurveyQuestionTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyQuestionBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>