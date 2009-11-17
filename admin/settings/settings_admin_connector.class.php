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
        $adm = AdminDataManager :: get_instance();
        $options = $adm->get_languages();

        return $options;
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
}
?>
