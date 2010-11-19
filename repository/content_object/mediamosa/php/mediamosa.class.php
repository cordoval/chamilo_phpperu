<?php
namespace repository\content_object\mediamosa;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/*
 * @author jevdheyd
 */
class Mediamosa extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }
}
?>