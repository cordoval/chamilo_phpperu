<?php
namespace repository\content_object\survey_open_question;

use common\libraries\Path;
use repository\ContentObjectDifference;

/**
 * @package repository.content_object.survey_open_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class can be used to get the difference between open question
 */
class SurveyOpenQuestionDifference extends ContentObjectDifference
{

    function get_difference()
    {
        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = $object->get_question_type();
        $version_string = $version->get_question_type();

        $td = new Difference_Engine($object_string, $version_string);

        return array_merge($td->getDiff(), parent :: get_difference());
    }
}