<?php
namespace repository\content_object\ordering_question;

use common\libraries\Utilities;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: ordering_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.ordering_question
 */
require_once dirname(__FILE__) . '/ordering_question_option.class.php';

class OrderingQuestion extends ContentObject implements Versionable
{
    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_HINT = 'hint';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    public function add_option($option)
    {
        $options = $this->get_options();
        $options[] = $option;
        return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
    }

    public function set_options($options)
    {
        return $this->set_additional_property(self :: PROPERTY_OPTIONS, serialize($options));
    }

    public function get_options()
    {
        if ($result = unserialize($this->get_additional_property(self :: PROPERTY_OPTIONS)))
        {
            return $result;
        }
        return array();
    }

    public function set_hint($hint)
    {
        return $this->set_additional_property(self :: PROPERTY_HINT, $hint);
    }

    public function get_hint()
    {
        return $this->get_additional_property(self :: PROPERTY_HINT);
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_OPTIONS, self :: PROPERTY_HINT);
    }
}
?>