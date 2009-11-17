<?php
/**
 * $Id: assessment_publication_user.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment
 */

/**
 * This class describes a AssessmentPublicationUser data object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';
    
    /**
     * AssessmentPublicationUser properties
     */
    const PROPERTY_ASSESSMENT_PUBLICATION = 'assessment_publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ASSESSMENT_PUBLICATION, self :: PROPERTY_USER);
    }

    function get_data_manager()
    {
        return AssessmentDataManager :: get_instance();
    }

    /**
     * Returns the assessment_publication of this AssessmentPublicationUser.
     * @return the assessment_publication.
     */
    function get_assessment_publication()
    {
        return $this->get_default_property(self :: PROPERTY_ASSESSMENT_PUBLICATION);
    }

    /**
     * Sets the assessment_publication of this AssessmentPublicationUser.
     * @param assessment_publication
     */
    function set_assessment_publication($assessment_publication)
    {
        $this->set_default_property(self :: PROPERTY_ASSESSMENT_PUBLICATION, $assessment_publication);
    }

    /**
     * Returns the user of this AssessmentPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this AssessmentPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        return $this->get_data_manager()->create_assessment_publication_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>