<?php
/**
 * $Id: wiki_page.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.wiki_page
 */
class WikiPage extends ContentObject implements Versionable, ForcedVersionSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>