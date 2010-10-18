<?php
namespace repository\content_object\description;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: description.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.description
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * A Description
 */
class Description extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>