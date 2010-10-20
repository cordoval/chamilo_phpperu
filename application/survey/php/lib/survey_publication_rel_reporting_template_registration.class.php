<?php

class SurveyPublicationRelReportingTemplateRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_rel_reporting_template_registration';
    
    const PROPERTY_ID = 'id';
    const PROPERTY_OWNER_ID = 'owner_id';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_REPORTING_TEMPLATE_REGISTRATION_ID = 'reporting_template_registration_id';
    const PROPERTY_LEVEL = 'level';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';

    public function create()
    {
        $succes = parent :: create();
        if ($succes)
        {
            $parent_location = SurveyRights :: get_surveys_subtree_root_id();
            $location = SurveyRights :: create_location_in_surveys_subtree($this->get_id(), $this->get_id(), $parent_location, SurveyRights :: TYPE_REPORTING_TEMPLATE_REGISTRATION, true);
            
            $rights = SurveyRights :: get_available_rights_for_reporting_template_registrations();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_owner_id(), $location->get_id(), 1);
            }
        }
     
        return $succes;
    }

    public function delete()
    {
        $location = SurveyRights :: get_location_by_identifier_from_surveys_subtree($this->get_id(), SurveyRights :: TYPE_REPORTING_TEMPLATE_REGISTRATION);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }
        $succes = parent :: delete();
        return $succes;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_REPORTING_TEMPLATE_REGISTRATION_ID, self :: PROPERTY_OWNER_ID, self :: PROPERTY_ID, self :: PROPERTY_LEVEL, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION);
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    /**
     * Returns the publication_id of this SurveyPublicationRelReportingTemplateRegistration.
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this SurveyPublicationRelReportingTemplateRegistration.
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the reporting_template_registration_id of this SurveyPublicationRelReportingTemplateRegistration.
     * @return the reporting_template_registration_id.
     */
    function get_reporting_template_registration_id()
    {
        return $this->get_default_property(self :: PROPERTY_REPORTING_TEMPLATE_REGISTRATION_ID);
    }

    /**
     * Sets the reporting_template_registration_id of this SurveyPublicationRelReportingTemplateRegistration.
     * @param reporting_template_registration_id
     */
    function set_reporting_template_registration_id($reporting_template_registration_id)
    {
        $this->set_default_property(self :: PROPERTY_REPORTING_TEMPLATE_REGISTRATION_ID, $reporting_template_registration_id);
    }

  /**
     * Sets the owner of this SurveyPublicationRelReportingTemplateRegistration.
     * @param owner
     */
    function set_owner_id($owner)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner);
    }
    
    /**
     * Returns the owner of this SurveyPublicationRelReportingTemplateRegistration.
     * @return owner.
     */
    function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    /**
     * Sets the level of this SurveyPublicationRelReportingTemplateRegistration.
     * @param level
     */
    function set_level($level)
    {
        $this->set_default_property(self :: PROPERTY_LEVEL, $level);
    }

    /**
     * Returns the level of this SurveyPublicationRelReportingTemplateRegistration.
     * @return level.
     */
    function get_level()
    {
        return $this->get_default_property(self :: PROPERTY_LEVEL);
    }

/**
     * Sets the name of this SurveyPublicationRelReportingTemplateRegistration.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the name of this SurveyPublicationRelReportingTemplateRegistration.
     * @return name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }
    
/**
     * Sets the description of this SurveyPublicationRelReportingTemplateRegistration.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the description of this SurveyPublicationRelReportingTemplateRegistration.
     * @return description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }
  

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}

?>