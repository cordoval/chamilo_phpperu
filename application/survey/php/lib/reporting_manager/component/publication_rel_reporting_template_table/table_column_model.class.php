<?php 
namespace survey;
use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;

require_once Path :: get_application_path() . 'lib/survey/tables/publication_rel_reporting_template_table/default_publication_rel_reporting_template_table_column_model.class.php';

class SurveyPublicationRelReportingTemplateTableColumnModel extends DefaultSurveyPublicationRelReportingTemplateTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function SurveyPublicationRelReportingTemplateTableColumnModel()
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