<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\StaticTableColumn;


class SurveyTableColumnModel extends DefaultSurveyTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($survey_context)
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