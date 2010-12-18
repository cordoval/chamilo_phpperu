<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * @package repository.content_object.survey_select_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveySelectQuestion extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_OPTIONS = 'options';
    const PROPERTY_ANSWER_TYPE = 'answer_type';

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

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ANSWER_TYPE, self :: PROPERTY_OPTIONS);
    }
}
?>