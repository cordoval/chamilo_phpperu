<?php
require_once dirname(__FILE__) . '/../../external_repository_object.class.php';

class GoogleDocsExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'google_docs';
    
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_VIEWED = 'viewed';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_MODIFIER_ID = 'modifier_id';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_VIEWED, self :: PROPERTY_CONTENT, self :: PROPERTY_MODIFIED, self :: PROPERTY_MODIFIER_ID));
    }

    function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    function set_modified($modified)
    {
        return $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    function get_viewed()
    {
        return $this->get_default_property(self :: PROPERTY_VIEWED);
    }

    function set_viewed($viewed)
    {
        return $this->set_default_property(self :: PROPERTY_VIEWED, $viewed);
    }

    function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    function set_content($content)
    {
        return $this->set_default_property(self :: PROPERTY_CONTENT, $content);
    }

    function get_modifier_id()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIER_ID);
    }

    function set_modifier_id($modifier_id)
    {
        return $this->set_default_property(self :: PROPERTY_MODIFIER_ID, $modifier_id);
    }
    
    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }
}
?>