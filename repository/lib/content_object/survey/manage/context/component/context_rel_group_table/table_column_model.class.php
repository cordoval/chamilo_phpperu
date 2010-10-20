<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/context_rel_group_table/default_context_rel_group_table_column_model.class.php';

class SurveyContextRelGroupTableColumnModel extends DefaultSurveyContextRelGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyContextRelGroupTableColumnModel($browser)
    {
        parent :: __construct();
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
