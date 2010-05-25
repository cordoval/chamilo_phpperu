<?php
/**
 * $Id: user_right_location.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib
 * @author Hans de Bisschop
 */


class PortfolioUserRightLocation extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_RIGHT_ID = 'right_id';
    const PROPERTY_LOCATION_ID = 'location_id';
    const PROPERTY_USER_ID = 'user_id';
   

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_RIGHT_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_LOCATION_ID));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager::get_instance();
    }

    function get_right_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
    }

    function set_right_id($right_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_location_id()
    {
        return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
    }

    function set_location_id($location_id)
    {
        $this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
    }
    

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>