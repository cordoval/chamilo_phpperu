<?php
/**
 * $Id: user_rights_template.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */


class UserRightsTemplate extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_RIGHTS_TEMPLATE_ID = 'rights_template_id';

    /**
     * Get the default properties of all users quota objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_RIGHTS_TEMPLATE_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_rights_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_RIGHTS_TEMPLATE_ID);
    }

    function set_rights_template_id($rights_template_id)
    {
        $this->set_default_property(self :: PROPERTY_RIGHTS_TEMPLATE_ID, $rights_template_id);
    }

    function create()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->create_user_rights_template($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>