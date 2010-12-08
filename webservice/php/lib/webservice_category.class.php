<?php
namespace webservice;

use common\libraries\Utilities;
use common\libraries\DataClass;
/**
 * $Id: webservice_category.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib
 */

/**
 * @package webservice
 */
/**
 * @author Stefan Billiet
 */

class WebserviceCategory extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_NAME = 'name';
    const PROPERTY_PARENT = 'parent_id';

    /**
     * Get the default properties of all webservice_categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_PARENT));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WebserviceDataManager :: get_instance();
    }

    /**
     * Returns the name of this webservice_category.
     * @return String The name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    function get_parent()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT);
    }

    /**
     * Sets the name of this webservice.
     * @param String $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    function set_parent($parent)
    {
        $this->set_default_property(self :: PROPERTY_PARENT, $parent);
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function create()
    {
        $wdm = WebserviceDataManager :: get_instance();
        $wdm->create_webservice_category($this);

        if ($this->get_parent())
        {
            $parent_id = WebserviceRights :: get_location_id_by_identifier_from_webservices_subtree(WebserviceRights :: TYPE_WEBSERVICE_CATEGORY, $this->get_parent());
        }
        else
        {
            $parent_id = WebserviceRights :: get_webservices_subtree_root_id();
        }

        if (! WebserviceRights :: create_location_in_webservice_subtree($this->get_name(), WebserviceRights :: TYPE_WEBSERVICE_CATEGORY, $this->get_id(), $parent_id))
        {
            return false;
        }

        return true;
    }
}
?>