<?php
namespace repository\content_object\survey_page;

use common\libraries\Path;
use common\libraries\ObjectTableColumnModel;
use common\libraries\StaticTableColumn;
use repository\DefaultContentObjectTableColumnModel;

/**
 * $Id: object_browser_table_column_model.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component.assessment_merger
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class ObjectBrowserTableColumnModel extends DefaultContentObjectTableColumnModel
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