<?php
/**
 * $Id: webservice_manager_component.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_manager
 */

/**
 * Base class for a webservice manager component.
 * A webservice manager provides different tools to the end user. Each tool is
 * represented by a webservice manager component and should extend this class.
 */

abstract class WebserviceManagerComponent extends CoreApplicationComponent
{

    /**
     * Constructor
     * @param WebserviceManager $groups_manager The user manager which
     * provides this component
     */
    function WebserviceManagerComponent($webservice_manager)
    {
        parent :: __construct($webservice_manager);
    }

    function retrieve_webservice_category($id)
    {
        return $this->get_parent()->retrieve_webservice_category($id);
    }

    function count_webservices($conditions = null)
    {
        return $this->get_parent()->count_webservices($conditions);
    }

    function get_manage_roles_url($webservice)
    {
        return $this->get_parent()->get_manage_roles_url($webservice);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }
}
?>