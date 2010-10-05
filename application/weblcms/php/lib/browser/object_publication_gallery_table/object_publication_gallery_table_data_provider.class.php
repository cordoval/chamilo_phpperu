<?php
/**
 *
 * @author Scaramanga
 */
require_once dirname(__FILE__) . '/../object_publication_table/object_publication_table_data_provider.class.php';

class ObjectPublicationGalleryTableDataProvider extends ObjectPublicationTableDataProvider
{
    function get_objects($offset, $count, $order_property = null)
    {
        return parent :: get_objects($offset, $count, $order_property);
    }
}
?>