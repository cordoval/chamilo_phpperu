<?php
/**
 * $Id: survey_publication_user.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey
 */

/**
 * This class describes a SurveyPublicationUser data object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';
    
    /**
     * SurveyPublicationUser properties
     */
    const PROPERTY_SURVEY_PUBLICATION = 'survey_publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_SURVEY_PUBLICATION, self :: PROPERTY_USER);
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    /**
     * Returns the survey_publication of this SurveyPublicationUser.
     * @return the survey_publication.
     */
    function get_survey_publication()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PUBLICATION);
    }

    /**
     * Sets the survey_publication of this SurveyPublicationUser.
     * @param survey_publication
     */
    function set_survey_publication($survey_publication)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PUBLICATION, $survey_publication);
    }

    /**
     * Returns the user of this SurveyPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this SurveyPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        return $this->get_data_manager()->create_survey_publication_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>