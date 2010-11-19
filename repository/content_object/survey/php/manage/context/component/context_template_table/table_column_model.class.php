<?php namespace repository\content_object\survey;

require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/context_template_table/default_context_template_table_column_model.class.php';


class SurveyContextTemplateBrowserTableColumnModel extends DefaultSurveyContextTemplateTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct();
        $this->set_default_order_column(0);
        $this->add_column(new StaticTableColumn(Translation :: get('Levels')));
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