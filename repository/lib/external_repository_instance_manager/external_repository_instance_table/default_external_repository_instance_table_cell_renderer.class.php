<?php
class DefaultExternalRepositoryInstanceTableCellRenderer extends ObjectTableCellRenderer
{

    function DefaultExternalRepositoryInstanceTableCellRenderer()
    {
    }

    function render_cell($column, $external_repository)
    {
        switch ($column->get_name())
        {
            case ExternalRepository :: PROPERTY_ID :
                return $external_repository->get_id();
            case ExternalRepository :: PROPERTY_TYPE :
                $type = $external_repository->get_type();
                return '<img src="' . Theme :: get_common_image_path() . 'external_repository/' . $type . '/logo/22.png" alt="' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($type))) . '"/>';
            case ExternalRepository :: PROPERTY_TITLE :
                return Utilities :: truncate_string($external_repository->get_title(), 50);
            case ExternalRepository :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($external_repository->get_description(), 50);
            case ExternalRepository :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $external_repository->get_creation_date());
            case ExternalRepository :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $external_repository->get_modification_date());
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