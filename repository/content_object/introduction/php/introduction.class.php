<?php
namespace repository\content_object\introduction;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: introduction.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.introduction
 */
/**
 * A Introduction
 */
class Introduction extends ContentObject implements Versionable
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
}

?>