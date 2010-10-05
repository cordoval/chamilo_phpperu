<?php
/**
 * This class describes a PeerAssessmentPublicationGroup data object
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */
class PeerAssessmentPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_group';
    
    /**
     * PeerAssessmentPublicationGroup properties
     */
    const PROPERTY_PEER_ASSESSMENT_PUBLICATION = 'peer_assessment_publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return PeerAssessmentDataManager :: get_instance();
    }

    /**
     * Returns the peer_assessment_publication of this PeerAssessmentPublicationGroup.
     * @return the peer_assessment_publication.
     */
    function get_peer_assessment_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION);
    }

    /**
     * Sets the peer_assessment_publication of this PeerAssessmentPublicationGroup.
     * @param peer_assessment_publication
     */
    function set_peer_assessment_publication($peer_assessment_publication)
    {
        $this->set_default_property(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, $peer_assessment_publication);
    }

    /**
     * Returns the group_id of this PeerAssessmentPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this PeerAssessmentPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        return $this->get_data_manager()->create_peer_assessment_publication_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>