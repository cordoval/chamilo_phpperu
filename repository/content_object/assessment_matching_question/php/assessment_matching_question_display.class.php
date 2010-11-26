<?php
namespace repository\content_object\assessment_matching_question;

use common\libraries\Path;
use repository\MatchingQuestionDisplay;

/**
 * $Id: assessment_matching_question_display.class.php $
 * @package repository.lib.content_object.matching_question
 */
require_once Path :: get_repository_path() . '/question_types/matching_question/matching_question_display.class.php';
require_once dirname (__FILE__) . '/assessment_matching_question_option.class.php';

class AssessmentMatchingQuestionDisplay extends MatchingQuestionDisplay
{

}
?>