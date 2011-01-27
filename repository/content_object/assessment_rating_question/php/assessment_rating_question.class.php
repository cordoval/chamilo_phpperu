<?php
namespace repository\content_object\assessment_rating_question;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Versionable;
use common\libraries\StringUtilities;
use repository\ContentObject;

/**
 * $Id: assessment_rating_question.class.php $
 * @package repository.lib.content_object.rating_question
 */
/**
 * This class represents an open question
 */
class AssessmentRatingQuestion extends ContentObject implements Versionable
{
    const PROPERTY_LOW = 'low';
    const PROPERTY_HIGH = 'high';
    const PROPERTY_CORRECT = 'correct';
    const PROPERTY_HINT = 'hint';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function get_correct()
    {
        return $this->get_additional_property(self :: PROPERTY_CORRECT);
    }

    function set_correct($value)
    {
        $this->set_additional_property(self :: PROPERTY_CORRECT, $value);
    }

    function get_low()
    {
        return $this->get_additional_property(self :: PROPERTY_LOW);
    }

    function get_high()
    {
        return $this->get_additional_property(self :: PROPERTY_HIGH);
    }

    function set_low($value)
    {
        $this->set_additional_property(self :: PROPERTY_LOW, $value);
    }

    function set_high($value)
    {
        $this->set_additional_property(self :: PROPERTY_HIGH, $value);
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
        return array(
                self :: PROPERTY_LOW,
                self :: PROPERTY_HIGH,
                self :: PROPERTY_CORRECT,
                self :: PROPERTY_HINT);
    }
}
?>