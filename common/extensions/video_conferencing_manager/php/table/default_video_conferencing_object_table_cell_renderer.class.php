<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;
use common\libraries\ObjectTableCellRenderer;
use common\libraries\DatetimeUtilities;

use repository\ContentObject;

class DefaultVideoConferencingObjectTableCellRenderer extends ObjectTableCellRenderer
{
    function __construct()
    {
    }

    function render_cell($column, $video_conferencing_object)
    {
        switch ($column->get_name())
        {
            case VideoConferencingObject :: PROPERTY_ID :
                return $video_conferencing_object->get_id();
            case VideoConferencingObject :: PROPERTY_TITLE :
                return $video_conferencing_object->get_title();
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