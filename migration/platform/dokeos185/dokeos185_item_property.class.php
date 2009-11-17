<?php

/**
 * $Id: dokeos185_item_property.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import.class.php';

/**
 * This class represents a dokeos 1.8.5 item property
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ItemProperty extends Import
{
    
    /**
     * Item Property properties
     */
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_INSERT_USER_ID = 'insert_user_id';
    const PROPERTY_INSERT_DATE = 'insert_date';
    const PROPERTY_LASTEDIT_DATE = 'lastedit_date';
    const PROPERTY_REF = 'ref';
    const PROPERTY_LASTEDIT_TYPE = 'lastedit_type';
    const PROPERTY_LASTEDIT_USER_ID = 'lastedit_user_id';
    const PROPERTY_TO_GROUP_ID = 'to_group_id';
    const PROPERTY_TO_USER_ID = 'to_user_id';
    const PROPERTY_VISIBILITY = 'visibility';
    const PROPERTY_START_VISIBLE = 'start_visible';
    const PROPERTY_END_VISIBLE = 'end_visible';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new dokeos185 Item Property object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185ItemProperty($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_TOOL, self :: PROPERTY_INSERT_USER_ID, self :: PROPERTY_INSERT_DATE, self :: PROPERTY_LASTEDIT_DATE, self :: PROPERTY_REF, self :: PROPERTY_LASTEDIT_TYPE, self :: PROPERTY_LASTEDIT_USER_ID, self :: PROPERTY_TO_GROUP_ID, self :: PROPERTY_TO_USER_ID, self :: PROPERTY_VISIBILITY, self :: PROPERTY_START_VISIBLE, self :: PROPERTY_END_VISIBLE);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the tool of this item property.
     * @return string The tool.
     */
    function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    /**
     * Returns the insert_user_id of this item property.
     * @return string the insert_user_id.
     */
    function get_insert_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_INSERT_USER_ID);
    }

    /**
     * Returns the insert_date of this item property.
     * @return string the insert_date.
     */
    function get_insert_date()
    {
        return $this->get_default_property(self :: PROPERTY_INSERT_DATE);
    }

    /**
     * Returns the lastedit_date of this item property.
     * @return date the lastedit_date.
     */
    function get_lastedit_date()
    {
        return $this->get_default_property(self :: PROPERTY_LASTEDIT_DATE);
    }

    /**
     * Returns the ref of this item property.
     * @return int the ref.
     */
    function get_ref()
    {
        return $this->get_default_property(self :: PROPERTY_REF);
    }

    /**
     * Returns the lastedit_type of this item property.
     * @return int the lastedit_type.
     */
    function get_lastedit_type()
    {
        return $this->get_default_property(self :: PROPERTY_LASTEDIT_TYPE);
    }

    /**
     * Returns the lastedit_user_id of this item property.
     * @return int the lastedit_user_id.
     */
    function get_lastedit_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_LASTEDIT_USER_ID);
    }

    /**
     * Returns the to_group_id of this item property.
     * @return int the to_group_id.
     */
    function get_to_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_TO_GROUP_ID);
    }

    /**
     * Returns the to_user_id of this item property.
     * @return int the to_user_id.
     */
    function get_to_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_TO_USER_ID);
    }

    /**
     * Returns the visibility of this item property.
     * @return int the visibility.
     */
    function get_visibility()
    {
        return $this->get_default_property(self :: PROPERTY_VISIBILITY);
    }

    /**
     * Returns the start_visible of this item property.
     * @return int the start_visible.
     */
    function get_start_visible()
    {
        return $this->get_default_property(self :: PROPERTY_START_VISIBLE);
    }

    /**
     * Returns the end_visible of this item property.
     * @return int the end_visible.
     */
    function get_end_visible()
    {
        return $this->get_default_property(self :: PROPERTY_END_VISIBLE);
    }
}
?>