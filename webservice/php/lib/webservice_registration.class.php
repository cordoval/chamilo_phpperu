<?php
namespace webservice;

use common\libraries\Utilities;
use common\libraries\DataClass;
/**
 * $Id: webservice_registration.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */

class WebserviceRegistration extends DataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_ACTIVE = 'active';
    const PROPERTY_CODE = 'code';
    const PROPERTY_CATEGORY = 'category_id';

    /**
     * Get the default properties of all webservices.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_CATEGORY, self :: PROPERTY_ACTIVE, self :: PROPERTY_CODE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WebserviceDataManager :: get_instance();
    }

    /**
     * Returns the code of this webservice.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
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
     * Sets the code of this webservice.
     */
    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
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

    function set_active($active)
    {
        $this->set_default_property(self :: PROPERTY_ACTIVE, $active);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function truncate()
    {
        return WebserviceDataManager :: get_instance()->truncate_webservice($this);
    }

    function create()
    {
        $wdm = WebserviceDataManager :: get_instance();
        $wdm->create_webservice($this);


        if ($this->get_category())
        {
            $parent_id = WebserviceRights :: get_location_id_by_identifier_from_webservices_subtree(WebserviceRights :: TYPE_WEBSERVICE_CATEGORY, $this->get_category());
        }
        else
        {
            $parent_id = WebserviceRights :: get_webservices_subtree_root_id();
        }

        if (! WebserviceRights :: create_location_in_webservice_subtree($this->get_name(), WebserviceRights :: TYPE_WEBSERVICE, $this->get_id(), $parent_id))
        {
            return false;
        }

        return true;
    }

}
?>