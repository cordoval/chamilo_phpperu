<?php
/**
 * This class describes a SurveyContextRelGroup data object
 * @author Sven Vanhoecke
 */
class SurveyContextRelGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * SurveyContextRelGroup properties
     */
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_GROUP_ID = 'group_id';
    const PROPERTY_CREATED = 'created';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CONTEXT_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_CREATED);
    }

    function get_data_manager()
    {
        return SurveyContextDataManager :: get_instance();
    }

    /**
     * Returns the context_id of this SurveyContextRelGroup.
     * @return the context_id.
     */
    function get_context_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    /**
     * Sets the context_id of this SurveyContextRelGroup.
     * @param context_id
     */
    function set_context_id($context_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    /**
     * Returns the group_id of this SurveyContextRelGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this SurveyContextRelGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    /**
     * Returns the created of this SurveyContextRelGroup.
     * @return the created.
     */
    function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     * Sets the created of this SurveyContextRelGroup.
     * @param created
     */
    function set_created($created)
    {
        $this->set_default_property(self :: PROPERTY_CREATED, $created);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>