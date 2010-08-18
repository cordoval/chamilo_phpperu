<?php
/**
 * @package rights.lib
 * @author Hans de Bisschop
 */
class RightsTypeTemplateRightLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_RIGHT_ID = 'right_id';
    const PROPERTY_RIGHTS_TYPE_TEMPLATE_ID = 'rights_type_template_id';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_TREE_TYPE = 'tree_type';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_RIGHT_ID, self :: PROPERTY_RIGHTS_TYPE_TEMPLATE_ID, self :: PROPERTY_APPLICATION, self :: PROPERTY_TREE_TYPE, self :: PROPERTY_TYPE, self :: PROPERTY_VALUE);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RightsDataManager :: get_instance();
    }

    function get_right_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
    }

    function set_right_id($right_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
    }

    function get_rights_type_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHTS_TYPE_TEMPLATE_ID);
    }

    function set_rights_type_template_id($rights_type_template_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHTS_TYPE_TEMPLATE_ID, $rights_type_template_id);
    }

    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }
    
    function get_tree_type()
    {
        return $this->get_default_property(self :: PROPERTY_TREE_TYPE);
    }

    function set_tree_type($tree_type)
    {
        $this->set_default_property(self :: PROPERTY_TREE_TYPE, $tree_type);
    }
    
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    function create()
    {
        $rdm = RightsDataManager :: get_instance();
        return $rdm->create_rights_type_template_right_location($this);
    }

    function invert()
    {
        $value = $this->get_value();
        $this->set_value(! $value);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>