<?php 
namespace repository\content_object\survey;

use common\libraries\Path;
use common\libraries\StaticTableColumn;


require_once Path :: get_repository_content_object_path() . '/survey/php/manage/context/tables/context_rel_group_table/default_context_rel_group_table_column_model.class.php';

class SurveyContextRelGroupTableColumnModel extends DefaultSurveyContextRelGroupTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function __construct($browser)
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
