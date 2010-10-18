<?php
/**
 * $Id: object_publication_table_data_provider.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.object_publication_table
 */
/**
 * This class represents a data provider for a publication candidate table
 */
class ObjectPublicationTableDataProvider extends ObjectTableDataProvider
{
    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        return WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($this->get_condition(), $order_property, $offset, $count);
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        return WeblcmsDataManager :: get_instance()->count_content_object_publications($this->get_condition());
    }
}
?>