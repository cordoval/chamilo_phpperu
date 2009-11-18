<?php
/**
 * $Id: rights_template_right_location.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */


class RightsTemplateRightLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_RIGHT_ID = 'right_id';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_RIGHTS_TEMPLATE_ID = 'rights_template_id';
    const PROPERTY_VALUE = 'value';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_RIGHT_ID, self :: PROPERTY_RIGHTS_TEMPLATE_ID, self :: PROPERTY_LOCATION_ID, self :: PROPERTY_VALUE);
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

    function get_rights_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHTS_TEMPLATE_ID);
    }

    function set_rights_template_id($rights_template_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template_id);
    }

    function get_location_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
    }

    function set_location_id($location_id)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
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
        return $rdm->create_rights_template_right_location($this);
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