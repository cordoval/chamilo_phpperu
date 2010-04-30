<?php

require_once dirname(__FILE__) . '/../../tables/survey_context_template_table/default_survey_context_template_table_column_model.class.php';

/**
 * Table column model for the user browser table
 */
class SurveyContextTemplateBrowserTableColumnModel extends DefaultSurveyContextTemplateTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyContextTemplateBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
        $this->add_column(new StaticTableColumn(Translation :: get('Locations')));
        $this->add_column(new StaticTableColumn(Translation :: get('Subcategories')));
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