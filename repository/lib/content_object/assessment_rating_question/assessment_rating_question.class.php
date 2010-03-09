<?php
/**
 * $Id: assessment_rating_question.class.php $
 * @package repository.lib.content_object.rating_question
 */
require_once PATH :: get_repository_path() . '/question_types/rating_question/rating_question.class.php';
/**
 * This class represents an open question
 */
class AssessmentRatingQuestion extends RatingQuestion
{
    const PROPERTY_CORRECT = 'correct';
    
    function get_correct()
    {
        return $this->get_additional_property(self :: PROPERTY_CORRECT);
    }

    function set_correct($value)
    {
        $this->set_additional_property(self :: PROPERTY_CORRECT, $value);
    }
	
    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_LOW, self :: PROPERTY_HIGH, self :: PROPERTY_CORRECT);
    }
}
?>