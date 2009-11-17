<?php
/**
 * $Id: assessment_publication_group.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment
 */

/**
 * This class describes a AssessmentPublicationGroup data object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_group';
    
    /**
     * AssessmentPublicationGroup properties
     */
    const PROPERTY_ASSESSMENT_PUBLICATION = 'assessment_publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ASSESSMENT_PUBLICATION, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return AssessmentDataManager :: get_instance();
    }

    /**
     * Returns the assessment_publication of this AssessmentPublicationGroup.
     * @return the assessment_publication.
     */
    function get_assessment_publication()
    {
        return $this->get_default_property(self :: PROPERTY_ASSESSMENT_PUBLICATION);
    }

    /**
     * Sets the assessment_publication of this AssessmentPublicationGroup.
     * @param assessment_publication
     */
    function set_assessment_publication($assessment_publication)
    {
        $this->set_default_property(self :: PROPERTY_ASSESSMENT_PUBLICATION, $assessment_publication);
    }

    /**
     * Returns the group_id of this AssessmentPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this AssessmentPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        return $this->get_data_manager()->create_assessment_publication_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>