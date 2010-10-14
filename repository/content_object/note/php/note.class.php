<?php
namespace repository\content_object\note;

use common\libraries\Utilities;

use repository\ContentObject;

/**
 * $Id: note.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.note
 */
/**
 * This class represents an note
 */
class Note extends ContentObject implements Versionable, AttachmentSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>