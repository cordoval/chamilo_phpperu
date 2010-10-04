<?php
/**
 * This class describes a Story data object
 *
 * @package repository.lib.content_object.story
 * @author Hans De Bisschop
 */

class Story extends ContentObject implements AttachmentSupport, Versionable
{
	const CLASS_NAME = __CLASS__;
	
	const ATTACHMENT_HEADER = 'header';

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
    function get_header($only_return_id = false)
    {
        $header = array_shift($this->get_headers());
        
        if (is_null($header))
        {
            return $only_return_id ? $header : false;
        }
        else
        {
            return $only_return_id ? $header->get_id() : $header;
        }
    }

    function get_headers()
    {
        return $this->get_attached_content_objects(self :: ATTACHMENT_HEADER);
    }

    function set_headers($headers = array())
    {
        $this->truncate_attachments(self :: ATTACHMENT_HEADER);
        $this->attach_content_objects($headers, self :: ATTACHMENT_HEADER);
    }
}
?>