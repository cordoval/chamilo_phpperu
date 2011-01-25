<?php
namespace repository\content_object\assessment_select_question;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\StringUtilities;
use repository\ContentObject;

/**
 * $Id: assessment_select_question.class.php $
 * @package repository.lib.content_object.select_question
 */
require_once dirname(__FILE__) . '/assessment_select_question_option.class.php';

class AssessmentSelectQuestion extends ContentObject
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_ANSWER_TYPE = 'answer_type';
    const PROPERTY_HINT = 'hint';

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

    /**
     * @return boolean
     */
    public function has_feedback()
    {
        foreach ($this->get_options() as $option)
        {
            if ($option->has_feedback())
            {
                return true;
            }
        }

        return false;
    }

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function get_answer_type()
    {
        return $this->get_additional_property(self :: PROPERTY_ANSWER_TYPE);
    }

    public function set_answer_type($answer_type)
    {
        return $this->set_additional_property(self :: PROPERTY_ANSWER_TYPE, $answer_type);
    }

    public function set_hint($hint)
    {
        return $this->set_additional_property(self :: PROPERTY_HINT, $hint);
    }

    public function get_hint()
    {
        return $this->get_additional_property(self :: PROPERTY_HINT);
    }

    function has_hint()
    {
        return StringUtilities :: has_value($this->get_hint(), true);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ANSWER_TYPE, self :: PROPERTY_OPTIONS, self :: PROPERTY_HINT);
    }

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>