<?php
namespace repository;

use common\libraries\Utilities;

/**
 * @package repository.lib
 */
/**
 *	@author Sven Vanpoucke
 */

class ContentObjectGroupShare extends ContentObjectShare
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_GROUP_ID = 'group_id';

    const TYPE_GROUP_SHARE = 'group';

    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }
    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_GROUP_ID));
    }


    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
//    	return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>