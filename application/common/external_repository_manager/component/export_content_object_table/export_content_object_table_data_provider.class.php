<?php
/**
 * $Id: content_object_table_data_provider.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component.content_object_table
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/content_object_table/content_object_table_data_provider.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class ExportContentObjectTableDataProvider extends ContentObjectTableDataProvider
{
    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function ExportContentObjectTableDataProvider($owner, $types, $query = null, $parent)
    {
        parent :: __construct($owner, $types, $query, $parent);
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        $dm = RepositoryDataManager :: get_instance();
        return $dm->retrieve_type_content_objects(Document :: get_type_name(), $this->get_condition(), $order_property, $offset, $count);
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->count_type_content_objects(Document :: get_type_name(), $this->get_condition());
    }

    function get_type_conditions()
    {
        return $this->get_parent()->get_parent()->get_content_object_type_conditions();
    }
}
?>