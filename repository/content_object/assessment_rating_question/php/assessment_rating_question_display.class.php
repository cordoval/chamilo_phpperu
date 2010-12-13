<?php
namespace repository\content_object\assessment_rating_question;

use common\libraries\ResourceManager;
use common\libraries\Path;

use repository\ContentObjectDisplay;

/**
 * $Id: assessment_rating_question_display.class.php $
 * @package repository.lib.content_object.rating_question
 */

/**
 * This class can be used to display open questions
 */
class AssessmentRatingQuestionDisplay extends ContentObjectDisplay
{

    function get_description()
    {
        $question = $this->get_content_object();
        $min = $question->get_low();
        $max = $question->get_high();

        $html = array();
        $html[] = parent :: get_description();
        $html[] = '<div class="question">';
        $html[] = '<div class="answer">';
        $html[] = '<select class="rating_slider">';
        $html[] = '</option>';
        for($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
            $html[] = '<option value="' . $i . '" > ' . $i . '';
            $html[] = '</option>';
        }
        $html[] = '</select>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get_repository_content_object_path(true) . 'assessment_rating_question/resources/javascript/assessment_rating_question.js');
        return implode("\n", $html);
    }
}
?>