<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: assessment_multiple_choice_question.class.php$
 * @package repository.lib.content_object.multiple_choice_question
 */
require_once dirname(__FILE__) . '/assessment_multiple_choice_question_option.class.php';

class AssessmentMultipleChoiceQuestion extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_ANSWER_TYPE = 'answer_type';
    const PROPERTY_OPTIONS = 'options';

    const ANSWER_TYPE_RADIO = 1;
    const ANSWER_TYPE_CHECKBOX = 2;

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

    public function get_number_of_options()
    {
        return count($this->get_options());
    }

    public function set_answer_type($type)
    {
        return $this->set_additional_property(self :: PROPERTY_ANSWER_TYPE, $type);
    }

    public function get_answer_type()
    {
        return $this->get_additional_property(self :: PROPERTY_ANSWER_TYPE);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ANSWER_TYPE, self :: PROPERTY_OPTIONS);
    }

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

}
?>