<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Utilities;
use common\libraries\Path;

use repository\SelectQuestion;

/**
 * $Id: survey_select_question.class.php $
 * @package repository.lib.content_object.survey_select_question
 */
require_once Path :: get_repository_path() . '/question_types/select_question/select_question.class.php';
require_once dirname(__FILE__) . '/survey_select_question_option.class.php';

class SurveySelectQuestion extends SelectQuestion
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>