<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Path;
use repository\ContentObjectDisplay;

/**
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class can be used to display open questions
 */
class SurveyOpenQuestionDisplay extends ContentObjectDisplay
{

    function get_description()
    {
        $description = parent :: get_description();
        $object = $this->get_content_object();

        return '<b>' . $description . '</b>';
    }
}