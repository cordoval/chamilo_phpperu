<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Utilities;
use common\libraries\Path;

use repository\OpenQuestion;

/**
 * $Id: survey_open_question.class.php $
 * @package repository.lib.content_object.survey_open_question
 */
require_once Path :: get_repository_path() . '/question_types/open_question/open_question.class.php';
/**
 * This class represents an open question
 */
class SurveyOpenQuestion extends OpenQuestion
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(array_pop(explode('\\', self :: CLASS_NAME)));
    }
}
?>