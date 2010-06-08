<?php
/**
 * Data provider for a peer_assessment_publication table
 * @author Nick Van Loocke
 */
class PeerAssessmentPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function PeerAssessmentPublicationBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Retrieves the objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of objects
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);
        
        $publications = $this->get_browser()->retrieve_peer_assessment_publications($this->get_condition(), $offset, $count)->as_array();
        foreach ($publications as &$publication)
        {
            $publication->set_content_object(RepositoryDataManager :: get_instance()->retrieve_content_object($publication->get_content_object()));
        }
        return $publications;
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_peer_assessment_publications($this->get_condition());
    }
}
?>