<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * $Id: quota_box_rel_category_rel_group.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class QuotaBoxRelCategoryRelGroup extends DataClass
{
    const PROPERTY_QUOTA_BOX_REL_CATEGORY_ID = 'quota_box_rel_category_id';
    const PROPERTY_GROUP_ID = 'group_id';
    
    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function get_quota_box_rel_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID);
    }

    function set_quota_box_rel_category_id($quota_box_rel_category_id)
    {
        $this->set_default_property(self :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category_id);
    }

    function create()
    {
        $rdm = ReservationsDataManager :: get_instance();
        return $rdm->create_quota_box_rel_category_rel_group($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>