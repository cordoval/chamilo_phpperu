<?php
/**
 * $Id: content_object_publication_group.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */

/**
 * This class describes a ContentObjectPublicationGroup data object
 *
 * @author Hans De Bisschop
 */
class ContentObjectPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * ContentObjectPublicationGroup properties
     */
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_GROUP_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Returns the publication_id of this ContentObjectPublicationGroup.
     * @return the publication_id.
     */
    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     * Sets the publication_id of this ContentObjectPublicationGroup.
     * @param publication_id
     */
    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    /**
     * Returns the group_id of this ContentObjectPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this ContentObjectPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        $dm = WeblcmsDataManager :: get_instance();
        return $dm->create_content_object_publication_group($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>