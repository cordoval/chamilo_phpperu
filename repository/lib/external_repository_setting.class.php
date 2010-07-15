<?php

/**
 * @author Hans De Bisschop
 */

class ExternalRepositorySetting extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_EXTERNAL_REPOSITORY_ID = 'external_repository_id';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all settings.
     * @return array The property names.
     */
    /**
     * @return array:
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE));
    }

    /**
     * @return RepositoryDataManagerInterface
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    /**
     * @return string the external repository id
     */
    function get_external_repository_id()
    {
        return $this->get_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID);
    }

    /**
     * Returns the variable of this setting object
     * @return string the variable
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this setting object
     * @return string the value
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * @param string $external_repository_id
     */
    function set_external_repository_id($external_repository_id)
    {
        $this->set_default_property(self :: PROPERTY_EXTERNAL_REPOSITORY_ID, $external_repository_id);
    }

    /**
     * Sets the variable of this setting.
     * @param string $variable the variable.
     */
    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    /**
     * Sets the value of this setting.
     * @param string $value the value.
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    /**
     * @return string
     */
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>