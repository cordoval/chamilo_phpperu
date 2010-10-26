<?php
namespace repository\content_object\assessment_rating_question;

use common\libraries\Utilities;
use common\libraries\Path;
use repository\RatingQuestion;

/**
 * $Id: assessment_rating_question.class.php $
 * @package repository.lib.content_object.rating_question
 */
require_once Path :: get_repository_path() . '/question_types/rating_question/rating_question.class.php';
/**
 * This class represents an open question
 */
class AssessmentRatingQuestion extends RatingQuestion
{
    const PROPERTY_CORRECT = 'correct';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

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