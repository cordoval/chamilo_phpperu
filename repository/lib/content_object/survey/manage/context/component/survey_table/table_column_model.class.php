<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/survey_table/default_survey_table_column_model.class.php';


class SurveyTableColumnModel extends DefaultSurveyTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyTableColumnModel($survey_context)
    {
        parent :: __construct($survey_context);
        $this->set_default_order_column(0);
        $this->add_column(self :: get_modification_column());
    }
    
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