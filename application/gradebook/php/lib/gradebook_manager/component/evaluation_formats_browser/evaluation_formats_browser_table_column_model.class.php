<?php
namespace application\gradebook;

use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;

require_once WebApplication :: get_application_class_lib_path('gradebook') . 'tables/evaluation_formats_table/default_evaluation_formats_table_column_model.class.php';

class EvaluationFormatsBrowserTableColumnModel extends DefaultEvaluationFormatsTableColumnModel
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
            self :: $modification_column = new StaticTableColumn(Translation :: get('Action', null, Utilities :: COMMON_LIBRARIES));
        }
        return self :: $modification_column;
    }

}

?>