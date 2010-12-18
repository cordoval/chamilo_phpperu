<?php
namespace repository\content_object\survey_rating_question;

use common\libraries\Path;
use repository\ContentObjectDisplay;

/**
 * @package repository.content_object.survey_rating_question
 * @author Eduard Vossen
 * @author Magali Gillard
 */
/**
 * This class can be used to display open questions
 */
class SurveyRatingQuestionDisplay extends ContentObjectDisplay
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
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/rating_question.js');
        return implode("\n", $html);
    }
}