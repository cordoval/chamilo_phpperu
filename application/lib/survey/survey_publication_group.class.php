<?php
/**
 * $Id: survey_publication_group.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey
 */

/**
 * This class describes a SurveyPublicationGroup data object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_group';
    
    /**
     * SurveyPublicationGroup properties
     */
    const PROPERTY_SURVEY_PUBLICATION = 'survey_publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_SURVEY_PUBLICATION, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    /**
     * Returns the survey_publication of this SurveyPublicationGroup.
     * @return the survey_publication.
     */
    function get_survey_publication()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PUBLICATION);
    }

    /**
     * Sets the survey_publication of this SurveyPublicationGroup.
     * @param survey_publication
     */
    function set_survey_publication($survey_publication)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PUBLICATION, $survey_publication);
    }

    /**
     * Returns the group_id of this SurveyPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this SurveyPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        return $this->get_data_manager()->create_survey_publication_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>