<?php
require_once Path :: get_repository_path() . 'lib/content_object/survey/manage/context/tables/template_rel_page_table/default_template_rel_page_table_column_model.class.php';

class SurveyContextTemplateRelPageTableColumnModel extends DefaultSurveyContextTemplateRelPageTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyContextTemplateRelPageTableColumnModel()
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