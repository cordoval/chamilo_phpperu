<?php
class ContentObjectAttachment extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_ATTACHMENT_ID = 'attachment_id';
    const PROPERTY_TYPE = 'type';

    /**
     * @return string
     */
    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
    
    /**
     * Get the default properties of all content object attachments.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_ATTACHMENT_ID, self :: PROPERTY_TYPE);
    }
    
    /* (non-PHPdoc)
     * @see common/database/DataClass#get_data_manager()
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }
    
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }
    
    function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }
    
    function get_attachment_id()
    {
        return $this->get_default_property(self :: PROPERTY_ATTACHMENT_ID);
    }
    
    function set_attachment_id($attachment_id)
    {
        $this->set_default_property(self :: PROPERTY_ATTACHMENT_ID, $attachment_id);
    }
    
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }
    
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }
    
    function get_attachment_object()
    {
        return $this->get_data_manager()->retrieve_content_object($this->get_attachment_id());
    }
}
?>