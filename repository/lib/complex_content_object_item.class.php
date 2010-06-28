<?php
/**
 * $Id: complex_content_object_item.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * Instances of this class group generic information about a complex learning object item
 *
 * @author Sven Vanpoucke
 */

class ComplexContentObjectItem extends DataClass
{

    const CLASS_NAME = __CLASS__;
    const PROPERTY_REF = 'ref_id';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_ADD_DATE = 'add_date';

    private $additionalProperties;

    function ComplexContentObjectItem($defaultProperties = array (), $additionalProperties = array())
    {
        parent :: __construct($defaultProperties);
        $this->additionalProperties = $additionalProperties;
    }

    function get_additional_property($name)
    {
        return $this->additionalProperties[$name];
    }

    /**
     * Gives a value to a given additional property
     * @param String $name the name of the additional property
     * @param Object $value the new value
     */
    function set_additional_property($name, $value)
    {
        $this->additionalProperties[$name] = $value;
    }

    /**
     * Retrieves the default properties of this class
     * @return Array of Objects
     */
    function get_additional_properties()
    {
        return $this->additionalProperties;
    }

    /**
     * Sets the default properties of this class
     * @param Array Of Objects $defaultProperties
     */
    function set_additional_properties($additionalProperties)
    {
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * Get the default property names
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_REF, self :: PROPERTY_PARENT, self :: PROPERTY_USER_ID, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_ADD_DATE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }

    /**
     * Gets the additional property names
     * Should be overridden when needed
     * @return array the aditional property names
     */
    static function get_additional_property_names()
    {
        return array();
    }

    function get_add_date()
    {
        return $this->get_default_property(self :: PROPERTY_ADD_DATE);
    }

    function set_add_date($add_date)
    {
        $this->set_default_property(self :: PROPERTY_ADD_DATE, $add_date);
    }

    function get_ref()
    {
        return $this->get_default_property(self :: PROPERTY_REF);
    }

    function get_ref_object()
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_ref());
    }

    function set_ref($ref)
    {
        $this->set_default_property(self :: PROPERTY_REF, $ref);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function get_parent_object()
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_parent());
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    function create()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $this->set_add_date(time());
        if(!$this->get_display_order())
        {
        	$this->set_display_order($rdm->select_next_display_order($this->get_parent()));
        }
        return $rdm->create_complex_content_object_item($this);
    }

    function update()
    {
        return RepositoryDataManager :: get_instance()->update_complex_content_object_item($this);
    }

    function delete()
    {
        return RepositoryDataManager :: get_instance()->delete_complex_content_object_item($this);
    }

    /**
     * Determines whether this complex learning object is an extended complex learning object or not
     */
    function is_extended()
    {
        return count($this->get_additional_property_names()) > 0;
    }

    /**
     * Retrieves the allowed types to add to this complex learning object item
     * @return Array of learning object types
     */
    function get_allowed_types()
    {
        return array();
    }

    function is_complex()
    {
        return count($this->get_allowed_types()) > 0;
    }

    /**
     * Creates an instance of an extended class with the given type
     * @param String type
     * @param Array defaultProperties
     * @param Array additionalProperties
     * @return ComplexContentObjectItem
     */
    static function factory($type, $defaultProperties = array(), $additionalProperties = array())
    {
        if ($type)
        {
            $class = 'Complex' . Utilities :: underscores_to_camelcase($type);
            require_once dirname(__FILE__) . '/content_object/' . $type . '/complex_' . $type . '.class.php';
            return new $class($defaultProperties, $additionalProperties);
        }
        else
        {
            return new self($defaultProperties, $additionalProperties);
        }
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>