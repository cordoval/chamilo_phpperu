<?php
namespace repository\content_object\assessment_open_question;

use common\libraries\Path;
use reposistory\OpenQuestionDifference;

/**
 * $Id: assessment_open_question_difference.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.open_question
 */
require_once Path :: get_repository_path() . '/question_types/open_question/open_question_difference.class.php';
/**
 * This class can be used to get the difference between open question
 */
class AssessmentOpenQuestionDifference extends OpenQuestionDifference
{
}
?>