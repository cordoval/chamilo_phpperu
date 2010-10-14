<?php
namespace repository\content_object\mediamosa;

use common\libraries\Utilities;

use repository\ContentObject;

/*
 * @author jevdheyd
 */
class Mediamosa extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>