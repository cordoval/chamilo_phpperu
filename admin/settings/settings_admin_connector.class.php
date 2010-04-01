<?php

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 * @package admin.settings
 * $Id: settings_admin_connector.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */

class SettingsAdminConnector
{

    function get_languages()
    {
        return AdminDataManager :: get_languages();
    }

    function get_themes()
    {
        $options = Theme :: get_themes();

        return $options;
    }

    function get_portal_homes()
    {
        $options = array();
        $rdm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
        $objects = $rdm->retrieve_type_content_objects('portal_home', $condition);

        if ($objects->size() == 0)
        {
            $options[0] = Translation :: get('CreatePortalHomeFirst');
        }
        else
        {
            while ($object = $objects->next_result())
            {
                $options[$object->get_id()] = $object->get_title();
            }
        }

        return $options;
    }

    function get_time_zones()
    {
		$content = file_get_contents(dirname(__FILE__) . '/timezones.txt');
		$content = explode("\n", $content);

		$timezones = array();

		foreach($content as $timezone)
		{
			$timezone = trim($timezone);
			$timezones[$timezone] = $timezone;
		}

		return $timezones;
    }

    function get_active_applications()
    {
        $adm = AdminDataManager :: get_instance();
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_STATUS, 1);
        $condition = new AndCondition($conditions);

        $registrations = $adm->retrieve_registrations($condition);

        $options = array();
        $options['home'] = Translation :: get('Homepage');

        while($registration = $registrations->next_result())
        {
            $options[$registration->get_name()] = Translation :: get(Utilities :: underscores_to_camelcase($registration->get_name()));
        }

        asort($options);

        return $options;
    }
}
?>
