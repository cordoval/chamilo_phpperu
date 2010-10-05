<?php
/**
 * $Id: alexia_publication_group.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia
 */
/**
 * This class describes a AlexiaPublicationGroup data object
 *
 * @author Sven Vanpoucke
 */
class AlexiaPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_group';
    
    /**
     * AlexiaPublicationGroup properties
     */
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_GROUP_ID);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AlexiaDataManager :: get_instance();
    }

    /**
     * Returns the publication of this AlexiaPublicationGroup.
     * @return the publication.
     */
    function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     * Sets the publication of this AlexiaPublicationGroup.
     * @param publication
     */
    function set_publication($publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $publication);
    }

    /**
     * Returns the group_id of this AlexiaPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this AlexiaPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        $dm = AlexiaDataManager :: get_instance();
        return $dm->create_alexia_publication_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>