<?php
/**
 * $Id: personal_message.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.personal_message
 */
/**
 * This class represents a personal message
 */
class PersonalMessage extends ContentObject implements Versionable, AttachmentSupport, ForcedVersionSupport
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>