<?php
namespace repository\content_object\assessment_rating_question;

use common\libraries\Path;

use repository\ComplexContentObjectItem;

/**
 * $Id: complex_assessment_rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rating_question
 */

/**
 * This class represents a complex exercise (used to create complex learning objects)
 */
class ComplexAssessmentRatingQuestion extends ComplexContentObjectItem
{

    const PROPERTY_WEIGHT = 'weight';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_WEIGHT);
    }

    function get_weight()
    {
        return $this->get_additional_property(self :: PROPERTY_WEIGHT);
    }

    function set_weight($value)
    {
        $this->set_additional_property(self :: PROPERTY_WEIGHT, $value);
    }
}
?>