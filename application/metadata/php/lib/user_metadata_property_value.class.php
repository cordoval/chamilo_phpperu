<?php
namespace application\metadata;
use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
/**
 * metadata
 */

/**
 * This class describes a MetadataPropertyValue data object
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class UserMetadataPropertyValue extends MetadataPropertyValue
{
    const CLASS_NAME = __CLASS__;

    /**
     * MetadataPropertyValue properties
     */
    const PROPERTY_USER_ID = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
            return array (self :: PROPERTY_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_PROPERTY_TYPE_ID, self :: PROPERTY_VALUE);
    }    

    /**
     * Returns the content_object_id of this MetadataPropertyValue.
     * @return the content_object_id.
     */
    function get_user_id()
    {
            return $this->get_default_property(self :: PROPERTY_user_ID);
    }

    /**
     * Sets the content_object_id of this MetadataPropertyValue.
     * @param content_object_id
     */
    function set_user_id($user_id)
    {
            $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    static function get_table_name()
    {
            return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }
}

?>