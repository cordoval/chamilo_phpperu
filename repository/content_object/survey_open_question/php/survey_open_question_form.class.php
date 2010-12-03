<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Path;
use repository\OpenQuestionForm;

/**
 * $Id: survey_open_question_form.class.php $
 * @package repository.lib.content_object.survey_open_question
 */

/**
 * This class represents a form to create or update open questions
 */
class SurveyOpenQuestionForm extends OpenQuestionForm
{

    // Inherited
    function create_content_object()
    {
        $object = new SurveyOpenQuestion();
        return parent :: create_content_object($object);
    }

}