<?php
namespace repository\content_object\announcement;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\AttachmentSupport;

use repository\ContentObject;

/**
 * $Id: announcement.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.announcement
 *
 */
/**
 * This class represents an announcement
 */
class Announcement extends ContentObject implements Versionable, AttachmentSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

}
?>