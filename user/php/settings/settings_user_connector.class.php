<?php
/**
 * $Id: settings_user_connector.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.settings
 */

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class SettingsUserConnector
{

    function get_fullname_formats()
    {
        return User :: get_fullname_format_options();
    }
}
?>