<?php
namespace repository;

use common\extensions\video_conferencing_manager\VideoConferencingManager;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\Theme;
use common\libraries\DatetimeUtilities;

class DefaultVideoConferencingInstanceTableCellRenderer extends ObjectTableCellRenderer
{

    function __construct()
    {
    }

    function render_cell($column, $video_conferencing)
    {
        switch ($column->get_name())
        {
            case VideoConferencing :: PROPERTY_ID :
                return $video_conferencing->get_id();
            case VideoConferencing :: PROPERTY_TYPE :
                $type = $video_conferencing->get_type();
                return '<img src="' . Theme :: get_image_path(VideoConferencingManager :: get_namespace($type)) . '/logo/22.png" alt="' . htmlentities(Translation :: get('TypeName', null, VideoConferencingManager :: get_namespace($type))) . '"/>';
            case VideoConferencing :: PROPERTY_TITLE :
                return Utilities :: truncate_string($external_repository->get_title(), 50);
            case VideoConferencing :: PROPERTY_DESCRIPTION :
                return Utilities :: truncate_string($external_repository->get_description(), 50);
            case VideoConferencing :: PROPERTY_CREATION_DATE :
                return DatetimeUtilities :: format_locale_date(null, $external_repository->get_creation_date());
            case VideoConferencing :: PROPERTY_MODIFICATION_DATE :
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