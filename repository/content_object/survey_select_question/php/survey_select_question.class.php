<?php
namespace repository\content_object\survey_select_question;

use common\libraries\Utilities;
use common\libraries\Path;

use repository\SelectQuestion;

/**
 * $Id: survey_select_question.class.php $
 * @package repository.lib.content_object.survey_select_question
 */

class SurveySelectQuestion extends SelectQuestion
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }
}
?>