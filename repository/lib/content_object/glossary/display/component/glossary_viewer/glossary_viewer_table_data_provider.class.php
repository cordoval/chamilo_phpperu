<?php
/**
 * $Id: glossary_viewer_table_data_provider.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary.component.glossary_viewer
 */
/**
 * This class represents a data provider for a results candidate table
 */
class GlossaryViewerTableDataProvider extends ObjectTableDataProvider
{
    /**
     * The user id of the current active user.
     */
    private $owner;
    
    private $parent;

    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function GlossaryViewerTableDataProvider($parent, $owner)
    {
        $this->owner = $owner;
        $this->parent = $parent;
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        $dm = RepositoryDataManager :: get_instance();
        
        return ($dm->retrieve_complex_content_object_items($this->parent->get_condition(), $order_property, $offset, $count));
    }

    function get_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();
        $count = $dm->count_complex_content_object_items($this->parent->get_condition());
        return $count;
    
    }

}
?>