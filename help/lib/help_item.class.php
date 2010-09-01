<?php

/**
 * $Id: help_item.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib
 */

require_once dirname(__FILE__) . '/help_rights.class.php';

class HelpItem extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_LANGUAGE = 'language';
    const PROPERTY_URL = 'url';

    /**
     * Get the default properties of all groups.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_URL, self :: PROPERTY_LANGUAGE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return HelpDataManager :: get_instance();
    }

    /**
     * Returns the name of this group.
     * @return String The name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the url of this group.
     * @return String The url
     */
    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    function get_language()
    {
        return $this->get_default_property(self :: PROPERTY_LANGUAGE);
    }

    /**
     * Sets the name of this group.
     * @param String $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the url of this group.
     * @param String $url the url.
     */
    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    function set_language($language)
    {
        $this->set_default_property(self :: PROPERTY_LANGUAGE, $language);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function create()
    {
        $hdm = $this->get_data_manager();

        if(!$hdm->create_help_item($this))
        {
            return false;
        }

        if(!HelpRights :: create_location_in_help_subtree($this->get_name(), $this->get_id(), HelpRights :: get_help_subtree_root_id()))
        {
            
        }
        return true;
    }
}
?>