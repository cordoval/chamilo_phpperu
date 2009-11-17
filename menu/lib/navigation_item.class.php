<?php
/**
 * $Id: navigation_item.class.php 223 2009-11-13 14:39:28Z vanpouckesven $
 * @package menu.lib
 */

class NavigationItem extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CATEGORY = 'category_id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_EXTRA = 'extra';
    const PROPERTY_URL = 'url';
    const PROPERTY_IS_CATEGORY = 'is_category';

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CATEGORY, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_APPLICATION, self :: PROPERTY_SECTION, self :: PROPERTY_EXTRA, self :: PROPERTY_URL, self :: PROPERTY_IS_CATEGORY));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return MenuDataManager :: get_instance();
    }

    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    function get_section()
    {
        return $this->get_default_property(self :: PROPERTY_SECTION);
    }

    function set_section($section)
    {
        $this->set_default_property(self :: PROPERTY_SECTION, $section);
    }

    function get_extra()
    {
        return $this->get_default_property(self :: PROPERTY_EXTRA);
    }

    function set_extra($extra)
    {
        $this->set_default_property(self :: PROPERTY_EXTRA, $extra);
    }

    function get_is_category()
    {
        return $this->get_default_property(self :: PROPERTY_IS_CATEGORY);
    }

    function set_is_category($is_category)
    {
        $this->set_default_property(self :: PROPERTY_IS_CATEGORY, $is_category);
    }

    function create()
    {
        $mdm = $this->get_data_manager();
        $id = $mdm->get_next_navigation_item_id();
        $condition = new EqualityCondition(self :: PROPERTY_CATEGORY, $this->get_category());
        $sort = $mdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_SORT, $condition);
        
        $this->set_sort($sort + 1);
        $this->set_id($id);
        $success = $mdm->create_navigation_item($this);
        if (! $success)
        {
            return false;
        }
        
        return true;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>