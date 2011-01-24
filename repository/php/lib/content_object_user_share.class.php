<?php
namespace repository;

use common\libraries\Utilities;
use user\UserDataManager;

/**
 * @package repository.lib
 */
/**
 * @author Sven Vanpoucke
 */

class ContentObjectUserShare extends ContentObjectShare
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_USER_ID = 'user_id';

    const TYPE_USER_SHARE = 'user';

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_user()
    {
        return UserDataManager :: get_instance()->retrieve_user($this->get_user_id());
    }

    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID));
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>