<?php
/**
 * $Id: wiki.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki
 */
class Wiki extends ContentObject
{
    const PROPERTY_LOCKED = 'locked';
    const PROPERTY_LINKS = 'links';

    function get_allowed_types()
    {
        return array('wiki_page');
    }

    function get_locked()
    {
        return $this->get_additional_property(self :: PROPERTY_LOCKED);
    }

    function set_locked($locked)
    {
        return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
    }

    function get_links()
    {
        return $this->get_additional_property(self :: PROPERTY_LINKS);
    }

    function set_links($links)
    {
        return $this->set_additional_property(self :: PROPERTY_LINKS, $links);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOCKED, self :: PROPERTY_LINKS);
    }
    
	function is_versionable()
    {
        return false;
    }
}
?>