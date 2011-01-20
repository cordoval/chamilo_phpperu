<?php
namespace repository\content_object\assessment_open_question;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * $Id: assessment_open_question.class.php $
 * @package repository.lib.content_object.assessment_open_question
 */

/**
 * This class represents an open question
 */
class AssessmentOpenQuestion extends ContentObject implements Versionable
{
    const PROPERTY_QUESTION_TYPE = 'question_type';
    const PROPERTY_FEEDBACK = 'feedback';
    const PROPERTY_HINT = 'hint';

    const TYPE_OPEN = 1;
    const TYPE_OPEN_WITH_DOCUMENT = 2;
    const TYPE_DOCUMENT = 3;

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    static function get_additional_property_names()
    {
        return array(
                self :: PROPERTY_QUESTION_TYPE,
                self :: PROPERTY_FEEDBACK,
                self :: PROPERTY_HINT);
    }

    function get_question_type()
    {
        return $this->get_additional_property(self :: PROPERTY_QUESTION_TYPE);
    }

    function set_question_type($question_type)
    {
        $this->set_additional_property(self :: PROPERTY_QUESTION_TYPE, $question_type);
    }

    function get_feedback()
    {
        return $this->get_additional_property(self :: PROPERTY_FEEDBACK);
    }

    function set_feedback($feedback)
    {
        $this->set_additional_property(self :: PROPERTY_FEEDBACK, $feedback);
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

    function get_types()
    {
        $types = array();
        $types[self :: TYPE_OPEN] = Translation :: get('OpenQuestion');
        $types[self :: TYPE_OPEN_WITH_DOCUMENT] = Translation :: get('OpenQuestionWithDocument');
        $types[self :: TYPE_DOCUMENT] = Translation :: get('DocumentQuestion');
        return $types;
    }

}
?>