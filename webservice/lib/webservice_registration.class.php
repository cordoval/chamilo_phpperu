<?php
/**
 * $Id: webservice_registration.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */

class WebserviceRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_PARENT = 'parent_id';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_CATEGORY = 'category_id';

    /**
     * Get the default properties of all webservices.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PARENT, self :: PROPERTY_ACTIVE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WebserviceDataManager :: get_instance();
    }

    /**
     * Returns the application of this webservice.
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Returns the name of this webservice.
     * @return String The name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the description of this webservice.
     * @return String The description
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    function get_active()
    {
        return $this->get_default_property(self :: PROPERTY_ACTIVE);
    }

    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Sets the application of this webservice.
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    /**
     * Sets the name of this webservice.
     * @param String $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the description of this webservice.
     * @param String $description the description.
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $active);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function truncate()
    {
        return WebserviceDataManager :: get_instance()->truncate_webservice($this);
    }

    function create()
    {
        $wdm = WebserviceDataManager :: get_instance();
        $wdm->create_webservice($this);

        /*$location = new Location();
        $location->set_location($this->get_name());
        $location->set_application('webservice');
        $location->set_type('webservice');
        $location->set_identifier($this->get_id());*/

        //echo $location->get_location();

        if ($this->get_parent())
        {
            $parent_id = WebserviceRights :: get_location_id_by_identifier_from_webservices_subtree(WebserviceRights :: TYPE_WEBSERVICE_CATEGORY, $this->get_parent());
        }
        else
        {
            $parent_id = WebserviceRights :: get_webservices_subtree_root_id();
        }


        if (!WebserviceRights :: create_location_in_webservice_subtree($this->get_name(), WebserviceRights :: TYPE_WEBSERVICE, $this->get_id(), $parent_id))
        {
            return false;
        }

        return true;
    }

}
?>