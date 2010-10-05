<?php

require_once dirname(__FILE__) . '/reservations_data_manager.class.php';

/**
 * $Id: quota_rel_quota_box.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations
 */
/**
 *	@author Sven Vanpoucke
 */

class QuotaRelQuotaBox extends DataClass
{
    const PROPERTY_QUOTA_ID = 'quota_id';
    const PROPERTY_QUOTA_BOX_ID = 'quota_box_id';
    
    const CLASS_NAME = __CLASS__;

    /**
     * Get the default properties of all contributions.
     * @return array The property titles.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_QUOTA_ID, self :: PROPERTY_QUOTA_BOX_ID);
    }

    function get_data_manager()
    {
        return ReservationsDataManager :: get_instance();
    }

    /**
     * Returns the quota_id of this contribution.
     * @return int The quota_id.
     */
    function get_quota_id()
    {
        return $this->get_default_property(self :: PROPERTY_QUOTA_ID);
    }

    /**
     * Sets the quota_box_id of this contribution.
     * @param int $quota_box_id The quota_box_id.
     */
    function set_quota_id($quota_id)
    {
        $this->set_default_property(self :: PROPERTY_QUOTA_ID, $quota_id);
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
        return $rdm->create_quota_rel_quota_box($this);
    }

    function delete()
    {
        return ReservationsDataManager :: get_instance()->delete_quota_rel_quota_box($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}