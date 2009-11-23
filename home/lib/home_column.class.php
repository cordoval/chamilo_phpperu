<?php
/**
 * $Id: home_column.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib
 */

class HomeColumn extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'column';

    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_ROW = 'row_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_WIDTH, self :: PROPERTY_ROW, self :: PROPERTY_USER));
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

    function get_width()
    {
        return $this->get_default_property(self :: PROPERTY_WIDTH);
    }

    function set_width($width)
    {
        $this->set_default_property(self :: PROPERTY_WIDTH, $width);
    }

    function get_row()
    {
        return $this->get_default_property(self :: PROPERTY_ROW);
    }

    function set_row($row)
    {
        $this->set_default_property(self :: PROPERTY_ROW, $row);
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
        $hdm = $this->get_data_manager();

        $condition = new EqualityCondition(self :: PROPERTY_ROW, $this->get_row());
        $sort = $hdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_SORT, $condition);
        $this->set_sort($sort + 1);

        $success = $hdm->create_home_column($this);
        if (! $success)
        {
            return false;
        }

        return true;
    }

    function is_empty()
    {
        $hdm = $this->get_data_manager();

        $condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $this->get_id());

        $blocks_count = $hdm->count_home_blocks($condition);

        return ($blocks_count == 0);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>