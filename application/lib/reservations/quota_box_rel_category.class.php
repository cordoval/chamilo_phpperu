<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * $Id: quota_box_rel_category.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class QuotaBoxRelCategory extends DataClass
{
    const PROPERTY_QUOTA_BOX_ID = 'quota_box_id';
    const PROPERTY_CATEGORY_ID = 'category_id';
    
    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_QUOTA_BOX_ID, self :: PROPERTY_CATEGORY_ID));
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    function set_category_id($category_id)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category_id);
    }

    function get_quota_box_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUOTA_BOX_ID);
    }

    function set_quota_box_id($quota_box_id)
    {
        $this->set_default_property(self :: PROPERTY_QUOTA_BOX_ID, $quota_box_id);
    }

    function create()
    {
        $rdm = ReservationsDataManager :: get_instance();
        $this->set_id($rdm->get_next_quota_box_rel_category_id());
        return $rdm->create_quota_box_rel_category($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}