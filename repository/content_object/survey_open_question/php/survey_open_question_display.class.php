<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Path;
use repository\OpenQuestionDisplay;


/**
 * $Id: survey_open_question_display.class.php $
 * @package repository.lib.content_object.survey_open_question
 */
/**
 * This class can be used to display open questions
 */
class SurveyOpenQuestionDisplay extends OpenQuestionDisplay
{
    function get_description()
    {
        $description = parent :: get_description();
        $object = $this->get_content_object();

        return '<b>' . $description;
    }
}