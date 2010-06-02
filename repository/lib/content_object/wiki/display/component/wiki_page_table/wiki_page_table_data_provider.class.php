<?php
/**
 * $Id: wiki_page_table_data_provider.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component.wiki_page_table
 */
/**
 * This class represents a data provider for a publication candidate table
 */
class WikiPageTableDataProvider extends ObjectTableDataProvider
{
    /**
     * The id of the current publication/wiki.
     */
    private $owner;
    /**
     * The possible types of learning objects which can be selected.
     */
    private $types;
    /**
     * The search query, or null if none.
     */
    private $query;
    /**
     * The pagebrowser.
     */
    private $parent;

    /**
     * Constructor.
     * @param int $owner The user id of the current active user.
     * @param array $types The possible types of learning objects which can be
     * selected.
     * @param string $query The search query.
     */
    function WikiPageTableDataProvider($parent, $owner)
    {
        $this->parent = $parent;
        $this->owner = $owner;
    }

    /*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $dm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->owner, ComplexContentObjectItem :: get_table_name());
        return $dm->retrieve_complex_content_object_items($condition, $order_property, $offset, $count);
    }

    /*
	 * Inherited
	 */
    function get_object_count()
    {
        return count($this->get_objects()->as_array());
    }
}
?>