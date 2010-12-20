<?php
namespace repository\content_object\survey_rating_question;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Versionable;

use repository\ContentObject;

/**
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class represents an open question
 */
class SurveyRatingQuestion extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_LOW = 'low';
    const PROPERTY_HIGH = 'high';

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
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

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOW, self :: PROPERTY_HIGH);
    }

}

?>