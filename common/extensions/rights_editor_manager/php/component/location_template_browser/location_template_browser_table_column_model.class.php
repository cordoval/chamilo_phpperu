<?php
namespace common\extensions\rights_editor_manager;

use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\StaticTableColumn;
use common\libraries\Translation;
use rights\DefaultRightsTemplateTableColumnModel;

/**
 * $Id: location_template_browser_table_column_model.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 */
require_once Path :: get_rights_path() . 'lib/tables/rights_template_table/default_rights_template_table_column_model.class.php';
/**
 * Table column model for the user browser table
 */
class LocationTemplateBrowserTableColumnModel extends DefaultRightsTemplateTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;
    private static $rights_columns;
    private $browser;

    /**
     * Constructor
     */
    function __construct($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
        $this->set_default_order_column(1);
        $this->set_columns(array_splice($this->get_columns(), 0, 1));
        $this->add_rights_columns();
        //		$this->add_column(self :: get_modification_column());
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

    static function is_rights_column($column)
    {
        return in_array($column, self :: $rights_columns);
    }

    function add_rights_columns()
    {
        $rights = $this->browser->get_available_rights();
        
        foreach ($rights as $right_name => $right_id)
        {
            $column_name = Utilities :: underscores_to_camelcase(strtolower($right_name));
            $column = new StaticTableColumn(Translation :: get($column_name));
            $this->add_column($column);
            self :: $rights_columns[] = $column;
        }
    }
}
?>