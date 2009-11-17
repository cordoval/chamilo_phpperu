<?php
/**
 * $Id: platform_category.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.category_manager
 */

abstract class PlatformCategory extends DataClass
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_DISPLAY_ORDER = 'display_order';

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_PARENT, self :: PROPERTY_DISPLAY_ORDER));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return null;
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
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
    
/*abstract function create();
	abstract function update();
	abstract function delete();*/
/*function create()
	{
		$rdm = ReservationsDataManager :: get_instance();
		$this->set_id($rdm->get_next_category_id());
		$this->set_display_order($rdm->select_next_display_order($this->get_parent()));
		return $rdm->create_category($this);
	}
	
	function update()
	{
		return ReservationsDataManager :: get_instance()->update_category($this);
	}
	
	function delete()
	{
		return ReservationsDataManager :: get_instance()->delete_category($this);
	}*/
}