<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * $Id: item.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class Item extends DataClass
{
    const PROPERTY_CATEGORY = 'category_id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_RESPONSIBLE = 'responsible';
    const PROPERTY_CREDITS = 'credits';
    const PROPERTY_BLACKOUT = 'blackout';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_SALTO_ID = 'salto_id';
    const PROPERTY_CREATOR = 'creator_id';
    
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;
    
    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CATEGORY, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_RESPONSIBLE, self :: PROPERTY_CREDITS, self :: PROPERTY_BLACKOUT, self :: PROPERTY_STATUS, self :: PROPERTY_SALTO_ID, self :: PROPERTY_CREATOR));
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function get_responsible()
    {
        return $this->get_default_property(self :: PROPERTY_RESPONSIBLE);
    }

    function set_responsible($responsible)
    {
        $this->set_default_property(self :: PROPERTY_RESPONSIBLE, $responsible);
    }

    function get_credits()
    {
        return $this->get_default_property(self :: PROPERTY_CREDITS);
    }

    function set_credits($credits)
    {
        $this->set_default_property(self :: PROPERTY_CREDITS, $credits);
    }

    function get_blackout()
    {
        return $this->get_default_property(self :: PROPERTY_BLACKOUT);
    }

    function set_blackout($blackout)
    {
        $this->set_default_property(self :: PROPERTY_BLACKOUT, $blackout);
    }

    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_salto_id()
    {
        return $this->get_default_property(self :: PROPERTY_SALTO_ID);
    }

    function set_salto_id($salto_id)
    {
        $this->set_default_property(self :: PROPERTY_SALTO_ID, $salto_id);
    }

    function get_creator()
    {
        return $this->get_default_property(self :: PROPERTY_CREATOR);
    }

    function set_creator($creator)
    {
        $this->set_default_property(self :: PROPERTY_CREATOR, $creator);
    }

    function create()
    {
        $rdm = ReservationsDataManager :: get_instance();
        $this->set_id($rdm->get_next_item_id());
        $succes = $rdm->create_item($this);
        
        if ($this->get_category() == 0)
            $parent_location = ReservationsRights :: get_root_id();
        else
            $parent_location = ReservationsRights :: get_location_id_by_identifier('category', $this->get_category());
        
        $succes &= ReservationsRights :: create_location($this->get_name(), 'item', $this->get_id(), true, $parent_location);
        
        return $succes;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}