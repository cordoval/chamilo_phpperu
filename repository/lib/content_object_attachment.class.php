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
        return parent :: get_default_property_names(array(self :: PROPERTY_COLUMN, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_APPLICATION, self :: PROPERTY_COMPONENT, self :: PROPERTY_VISIBILITY, self :: PROPERTY_USER));
    }
    
    /* (non-PHPdoc)
     * @see common/database/DataClass#get_data_manager()
     */
    function get_data_manager()
    {
        return RepositoryDataManager :: get_instance();
    }
}
?>