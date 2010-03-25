<?php

require_once dirname(__FILE__) . '/../../../tables/survey_publication_table/default_survey_publication_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../survey_publication.class.php';

class TestcaseSurveyPublicationBrowserTableColumnModel extends DefaultSurveyPublicationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function TestcaseSurveyPublicationBrowserTableColumnModel()
    {
        parent :: __construct();
        $this->set_default_order_column(1);
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