<?php
namespace repository\content_object\handbook_topic;

use common\libraries\Utilities;
use common\libraries\Versionable;
use common\libraries\AttachmentSupport;

use repository\ContentObject;

/**
 * $Id: handbook_topic.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.handbook_topic
 */
/**
 * This class represents an handbook_topic
 */
class HandbookTopic extends ContentObject
{
	const CLASS_NAME = __CLASS__;
        const PROPERTY_TEXT = 'text';

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

        static function get_additional_property_names() 
        {
           return array(self :: PROPERTY_TEXT);
        }

        function get_text() {
        return $this->get_additional_property(self :: PROPERTY_TEXT);
    }

    function set_text($text) {
        $this->set_additional_property(self :: PROPERTY_TEXT, $text);
    }
}
?>