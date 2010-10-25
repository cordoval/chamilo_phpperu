<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\GalleryObjectTable;

class ExternalRepositoryBrowserGalleryTable
{

    static function factory($type, $browser, $parameters, $condition)
    {
        $class = 'common\extensions\external_repository_manager\implementation\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'ExternalRepositoryGalleryTable';
        require_once Path :: get_common_extensions_path() . 'external_repository_manager/implementation/' . $type . '/php/component/' . $type . '_external_repository_gallery_table/' . $type . '_external_repository_gallery_table.class.php';
        return new $class($browser, $parameters, $condition);
    }
}
?>