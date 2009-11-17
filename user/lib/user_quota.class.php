<?php
/**
 * $Id: user_quota.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib
 */

/**
 *	This class represents the different quota values for a user. (for each learning object type)
 *
 *	User quota objects have a number of default properties:
 *	- user_id: the user_id;
 *	- learning object type: the learning object type;
 *	- user_quota: the user quota:
 *
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */


class UserQuota extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONTENT_OBJECT_TYPE = 'content_object_type';
    const PROPERTY_USER_QUOTA = 'user_quota';

    /**
     * Get the default properties of all users quota objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_USER_ID, self :: PROPERTY_CONTENT_OBJECT_TYPE, self :: PROPERTY_USER_QUOTA);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return UserDataManager :: get_instance();
    }

    /**
     * Returns the user_id of this user.
     * @return int The user_id.
     */
    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Returns the learning object type.
     * @return String The lastname
     */
    function get_content_object_type()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE);
    }

    /**
     * Returns the user quota.
     * @return String The user quota.
     */
    function get_user_quota()
    {
        return $this->get_default_property(self :: PROPERTY_USER_QUOTA);
    }

    /**
     * Sets the user_id of this user.
     * @param int $user_id The user_id.
     */
    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Sets the learning object type.
     * @param $type the learning object type.
     */
    function set_content_object_type($type)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TYPE, $type);
    }

    /**
     * Sets the user quota.
     * @param $quota the quota
     */
    function set_user_quota($quota)
    {
        $this->set_default_property(self :: PROPERTY_USER_QUOTA, $quota);
    }

    function create()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->create_user_quota($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>