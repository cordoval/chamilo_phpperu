<?php
namespace repository\content_object\assessment_multiple_choice_question;

use common\libraries\Utilities;
use common\libraries\Path;
use repository\MultipleChoiceQuestion;

/**
 * $Id: assessment_multiple_choice_question.class.php$
 * @package repository.lib.content_object.multiple_choice_question
 */
require_once Path :: get_repository_path() . '/question_types/multiple_choice_question/multiple_choice_question.class.php';
require_once dirname(__FILE__) . '/assessment_multiple_choice_question_option.class.php';

class AssessmentMultipleChoiceQuestion extends MultipleChoiceQuestion
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
}
?>