<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\ObjectTable;

class VideoConferencingBrowserTable extends ObjectTable
{
    static function factory($type, $browser, $parameters, $condition)
    {
        $class = 'common\extensions\video_conferencing_manager\implementation\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'VideoConferencingTable';
        require_once Path :: get_common_extensions_path() . 'video_conferencing_manager/implementation/' . $type . '/php/component/' . $type . '_video_conferencing_table/' . $type . '_video_conferencing_table.class.php';
        return new $class($browser, $parameters, $condition);
    }
}
?>