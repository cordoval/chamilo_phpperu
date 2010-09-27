<?php
require_once dirname(__FILE__) . '/../../metadata_namespace.class.php';

class DefaultMetadataNamespaceTableColumnModel extends ObjectTableColumnModel
{
    function DefaultMetadataNamespaceTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
//        $columns[] = new StaticTableColumn('', false);
        //$columns[] = new ObjectTableColumn(MetadataNamespace :: PROPERTY_ID, true);
        $columns[] = new ObjectTableColumn(MetadataNamespace :: PROPERTY_NS_PREFIX, true);
        $columns[] = new ObjectTableColumn(MetadataNamespace :: PROPERTY_NAME, true);
        $columns[] = new ObjectTableColumn(MetadataNamespace :: PROPERTY_URL, true);

        return $columns;
    }
}
?>