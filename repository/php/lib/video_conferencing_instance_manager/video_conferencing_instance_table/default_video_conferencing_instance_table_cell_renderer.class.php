<?php
namespace repository;

use common\extensions\external_instance_manager\ExternalInstanceManager;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Theme;
use common\libraries\DatetimeUtilities;

class DefaultExternalInstanceInstanceTableCellRenderer extends ObjectTableCellRenderer
{

    function __construct()
    {
    }

    function render_cell($column, $external_instance)
    {
        switch ($column->get_name())
        {
            case ExternalInstance :: PROPERTY_ID :
                return $external_instance->get_id();
            case ExternalInstance :: PROPERTY_TYPE :
                $type = $external_instance->get_type();
                return '<img src="' . Theme :: get_image_path(ExternalInstanceManager :: get_namespace($type)) . '/logo/22.png" alt="' . htmlentities(Translation :: get('TypeName', null, ExternalInstanceManager :: get_namespace($type))) . '"/>';
            case ExternalInstance :: PROPERTY_TITLE :
                return Utilities :: truncate_string($external_instance->get_title(), 50);
            case ExternalInstance :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($external_instance->get_description(), 50);
            case ExternalInstance :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $external_instance->get_creation_date());
            case ExternalInstance :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $external_instance->get_modification_date());
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