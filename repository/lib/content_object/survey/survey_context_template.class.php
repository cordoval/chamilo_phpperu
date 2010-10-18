<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey_context_manager_rights.class.php';

class SurveyContextTemplate extends NestedTreeNode
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * SurveyContextTemplate properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_CONTEXT_TYPE = 'context_type';
    const PROPERTY_CONTEXT_TYPE_NAME = 'context_type_name';
    const PROPERTY_KEY = 'key_name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_OWNER_ID = 'owner_id';
	const PROPERTY_CONTEXT_REGISTRATION_ID = 'context_registration_id';
    
    public function create()
    {
        $succes = parent :: create();
        if ($succes)
        {
            $parent_location = SurveyContextManagerRights :: get_survey_context_manager_subtree_root_id();
            $location = SurveyContextManagerRights :: create_location_in_survey_context_manager_subtree($this->get_name(), $this->get_id(), $parent_location, SurveyContextManagerRights :: TYPE_CONTEXT_TEMPLATE, true);
            
            $rights = SurveyContextManagerRights :: get_available_rights_for_context_templates();
            foreach ($rights as $right)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_owner_id(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function delete()
    {
        $location = SurveyContextManagerRights :: get_location_by_identifier_from_survey_context_manager_subtree($this->get_id(), SurveyContextManagerRights :: TYPE_CONTEXT_TEMPLATE);
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
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CONTEXT_TYPE, self :: PROPERTY_CONTEXT_TYPE_NAME, self :: PROPERTY_KEY, self :: PROPERTY_TYPE, self :: PROPERTY_OWNER_ID, self :: PROPERTY_CONTEXT_REGISTRATION_ID));
    }

    /**
     * Returns the id of this SurveyContextTemplate.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Sets the id of this SurveyContextTemplate.
     * @param id
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    /**
     * Returns the name of this SurveyContextTemplate.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this SurveyContextTemplate.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the description of this SurveyContextTemplate.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this SurveyContextTemplate.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the context_type of this SurveyContextTemplate.
     * @return the context_type.
     */
    function get_context_type()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TYPE);
    }

    /**
     * Sets the context_type of this SurveyContextTemplate.
     * @param context_type
     */
    function set_context_type($context_type)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TYPE, $context_type);
    }

    /**
     * Returns the context_type_name of this SurveyContextTemplate.
     * @return the context_type_name.
     */
    function get_context_type_name()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TYPE);
    }

    /**
     * Sets the context_type_name of this SurveyContextTemplate.
     * @param context_type_name
     */
    function set_context_type_name($context_type_name)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TYPE_NAME, $context_type_name);
    }

    /**
     * Returns the keys of this SurveyContextTemplate.
     * @return the keys.
     */
    function get_key()
    {
        return $this->get_default_property(self :: PROPERTY_KEY);
    }

    /**
     * Sets the keys of this SurveyContextTemplate.
     * @param keys
     */
    function set_key($key)
    {
        $this->set_default_property(self :: PROPERTY_KEY, $key);
    }

    /**
     * Returns the Type of this SurveyContextTemplate.
     * @return the Type.
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the Type of this SurveyContextTemplate.
     * @param Type
     */
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function get_owner_id()
    {
        return $this->get_default_property(self :: PROPERTY_OWNER_ID);
    }

    function set_owner_id($owner_id)
    {
        $this->set_default_property(self :: PROPERTY_OWNER_ID, $owner_id);
    }

 	function get_context_registration_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_REGISTRATION_ID);
    }

    function set_context_registration_id($context_registration_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_REGISTRATION_ID, $context_registration_id);
    }
    
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_data_manager()
    {
        return SurveyContextDataManager :: get_instance();
    }

    function update()
    {
        $this->get_data_manager()->update_survey_context_template($this);
    }

    function get_level_count()
    {
        return $this->count_children(true) + 1;
    }
}

?>