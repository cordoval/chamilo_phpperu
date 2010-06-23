<?php
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumPost extends ContentObject implements Versionable, AttachmentSupport
{
    // Stores whether the user should get notified if
    // someone replies to the topic.
    const PROPERTY_NOTIFICATION = 'reply_notification';
    const NOTIFY_NONE = 1;
    const NOTIFY_TOPIC = 2;

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>