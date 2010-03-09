<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';
require_once dirname(__FILE__) . '/reservations_rights.class.php';

/**
 * $Id: category.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class Category extends DataClass
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_POOL = 'pool';
    const PROPERTY_STATUS = 'status';

    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const USE_AS_POOL = 1;
    const DONT_USE_AS_POOL = 0;

    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_POOL, self :: PROPERTY_STATUS));
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_pool()
    {
        return $this->get_default_property(self :: PROPERTY_POOL);
    }

    function set_pool($pool)
    {
        $this->set_default_property(self :: PROPERTY_POOL, $pool);
    }

    function use_as_pool()
    {
        return ($this->get_pool() == self :: USE_AS_POOL);
    }

    function not_use_as_pool()
    {
        return ($this->get_pool() == self :: DONT_USE_AS_POOL);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function create()
    {
        $rdm = ReservationsDataManager :: get_instance();
        $this->set_display_order($rdm->select_next_display_order($this->get_parent()));
        $succes = $rdm->create_category($this);

        if ($this->get_parent() == 0)
            $parent_location = ReservationsRights :: get_reservations_subtree_root_id();
        else
            $parent_location = ReservationsRights :: get_location_id_by_identifier_from_reservations_subtree('category', $this->get_parent());

        $succes &= ReservationsRights :: create_location_in_reservations_subtree($this->get_name(), 'category', $this->get_id(), $parent_location);

        return $succes;
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function delete()
    {
        $succes = parent :: delete();
        $categories = $this->retrieve_sub_categories($this->get_id(), true);
        foreach ($categories as $category)
        {
            $succes &= $category->delete();
        }

        return $succes;
    }

    static function retrieve_sub_categories($category_id, $recursive = false)
    {
        $rdm = ReservationsDataManager :: get_instance();

        $condition = new EqualityCondition(self :: PROPERTY_PARENT, $category_id);
        $categories = $rdm->retrieve_categories($condition);

        $subcategories = array();

        while ($category = $categories->next_result())
        {
            $subcategories[$category->get_id()] = $category;

            if ($recursive)
            {
                $subcategories += self :: retrieve_sub_categories($category->get_id(), $recursive);
            }
        }

        return $subcategories;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}