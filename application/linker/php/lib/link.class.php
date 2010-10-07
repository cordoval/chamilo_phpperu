<?php
/**
 * $Id: link.class.php 199 2009-11-13 12:23:04Z chellee $
 * @package application.lib.linker
 */
require_once WebApplication :: get_application_class_lib_path('linker') . 'linker_data_manager.class.php';

/**
 *  @author Sven Vanpoucke
 */

class Link extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_URL = 'url';

    /**
     * Get the default properties of all links.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_URL);
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return LinkerDataManager :: get_instance();
    }

    /**
     * Returns the name of this link.
     * @return String The name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the description of this link.
     * @return String The description
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the url of this link.
     * @return String The url
     */
    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    /**
     * Sets the name of this link.
     * @param String $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the description of this link.
     * @param String $description the description.
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Sets the url of this link.
     * @param String $url the url.
     */
    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

}
?>