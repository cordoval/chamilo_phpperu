<?php
namespace application\metadata;
require_once dirname(__FILE__) . '../../../tables/metadata_namespace_table/default_metadata_namespace_table_column_model.class.php';

/**
 * Table column model for the user browser table
 */
class MetadataNamespaceBrowserTableColumnModel extends DefaultMetadataNamespaceTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;

    /**
     * Constructor
     */
    function MetadataNamespaceBrowserTableColumnModel()
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