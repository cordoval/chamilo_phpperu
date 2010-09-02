<?php

/**
 * $Id: admin_rights.class.php 184 2009-11-13 09:51:32Z vanpouckesven $
 * @package admin.lib
 */
class AdminRights extends RightsUtilities
{
    const RIGHT_VIEW = 1;
    const RIGHT_ADD = 2;
    const RIGHT_EDIT = 3;
    const RIGHT_DELETE = 4;

    const LOCATION_SYSTEM_ANNOUNCEMENTS = 1;
    const LOCATION_SETTINGS = 2;
    const LOCATION_CATEGORY_MANAGER = 3;

    const TYPE_ROOT = 0;
    const TYPE_ADMIN_COMPONENT = 1;

    static function get_available_rights()
    {
        return parent :: get_available_rights(AdminManager :: APPLICATION_NAME);
    }

    static function is_allowed($right, $location = 0, $type = self :: TYPE_ROOT)
    {
        return parent :: is_allowed($right, $location, $type, AdminManager :: APPLICATION_NAME);
    }
}
?>