<?php
namespace repository\content_object\wiki_page;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\ForcedVersionSupport;

use repository\ContentObject;

/**
 * $Id: wiki_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki_page
 */
class WikiPage extends ContentObject implements Versionable, ForcedVersionSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
}
?>