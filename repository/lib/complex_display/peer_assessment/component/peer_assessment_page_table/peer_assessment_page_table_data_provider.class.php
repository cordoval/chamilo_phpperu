<?php
/**
 * This class represents a data provider for a publication candidate table
 * 
 * author: Nick Van Loocke
 */
class PeerAssessmentPageTableDataProvider extends ObjectTableDataProvider
{
    /**
     * The id of the current publication/peer_assessment.
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
    function PeerAssessmentPageTableDataProvider($parent, $owner)
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