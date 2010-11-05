<?php
namespace repository\content_object\blog_item;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\AttachmentSupport;

use repository\ContentObject;

/**
 * $Id: blog_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.blog_item
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
/**
 * This class represents an blog_item
 */
class BlogItem extends ContentObject implements Versionable, AttachmentSupport
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>