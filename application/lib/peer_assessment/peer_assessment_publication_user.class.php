<?php
/**
 * This class describes a PeerAssessmentPublicationUser data object
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */
class PeerAssessmentPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';
    
    /**
     * PeerAssessmentPublicationUser properties
     */
    const PROPERTY_PEER_ASSESSMENT_PUBLICATION = 'peer_assessment_publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, self :: PROPERTY_USER);
    }

    function get_data_manager()
    {
        return PeerAssessmentDataManager :: get_instance();
    }

    /**
     * Returns the peer_assessment_publication of this PeerAssessmentPublicationUser.
     * @return the peer_assessment_publication.
     */
    function get_peer_assessment_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION);
    }

    /**
     * Sets the peer_assessment_publication of this PeerAssessmentPublicationUser.
     * @param peer_assessment_publication
     */
    function set_peer_assessment_publication($peer_assessment_publication)
    {
        $this->set_default_property(self :: PROPERTY_PEER_ASSESSMENT_PUBLICATION, $peer_assessment_publication);
    }

    /**
     * Returns the user of this PeerAssessmentPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this PeerAssessmentPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
		return $this->get_data_manager()->create_peer_assessment_publication_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>