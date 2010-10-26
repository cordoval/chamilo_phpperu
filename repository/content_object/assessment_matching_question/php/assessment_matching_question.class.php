<?php
namespace repository\content_object\assessment_matching_question;

use common\libraries\Utilities;
use common\libraries\Path;

use repository\MatchingQuestion;

/**
 * $Id: assessment_matching_question.class.php $
 * @package repository.lib.content_object.matching_question
 */
require_once Path :: get_repository_path() . '/question_types/matching_question/matching_question.class.php';
require_once dirname(__FILE__) . '/assessment_matching_question_option.class.php';

class AssessmentMatchingQuestion extends MatchingQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
}
?>