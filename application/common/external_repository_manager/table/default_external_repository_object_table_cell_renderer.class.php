<?php
class DefaultExternalRepositoryObjectTableCellRenderer extends ObjectTableCellRenderer
{
    function DefaultExternalRepositoryObjectTableCellRenderer()
    {
    }

    function render_cell($column, $external_repository_object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                return $external_repository_object->get_icon_image();
            case ExternalRepositoryObject :: PROPERTY_ID :
                return $external_repository_object->get_id();
            case ExternalRepositoryObject :: PROPERTY_TITLE :
                return Utilities :: truncate_string($external_repository_object->get_title(), 50);
            case ExternalRepositoryObject :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($external_repository_object->get_description(), 50);
            case ExternalRepositoryObject :: PROPERTY_CREATED :
                return DatetimeUtilities :: format_locale_date(null, $external_repository_object->get_created());
            case ExternalRepositoryObject :: PROPERTY_OWNER_ID :
                return $external_repository_object->get_owner_id();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>