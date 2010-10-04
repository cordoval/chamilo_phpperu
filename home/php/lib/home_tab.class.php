<?php
/**
 * $Id: home_tab.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib
 */

class HomeTab extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'tab';

    const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_USER = 'user_id';

    private $defaultProperties;

    function HomeTab($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_USER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return HomeDataManager :: get_instance();
    }

    function get_sort()
    {
        return $this->get_default_property(self :: PROPERTY_SORT);
    }

    function set_sort($sort)
    {
        $this->set_default_property(self :: PROPERTY_SORT, $sort);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        $wdm = $this->get_data_manager();

        $condition = new EqualityCondition(self :: PROPERTY_USER, $this->get_user());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_SORT, $condition);
        $this->set_sort($sort + 1);

        $success = $wdm->create_home_tab($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }

    function can_be_deleted()
    {
        $hdm = $this->get_data_manager();
        $blocks = $hdm->retrieve_home_tab_blocks($this);

        while ($block = $blocks->next_result())
        {
            $application = $block->get_application();
            if ($application == 'admin' || $application == 'user')
            {
                return false;
            }
        }

        return true;
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>