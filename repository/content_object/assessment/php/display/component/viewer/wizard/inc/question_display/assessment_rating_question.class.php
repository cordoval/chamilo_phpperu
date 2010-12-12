<?php
namespace repository\content_object\assessment;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ResourceManager;
use repository\content_object\assessment_rating_question\AssessmentRatingQuestion;

/**
 * $Id: rating_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class AssessmentRatingQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $renderer = $this->get_renderer();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();

        $min = $question->get_low();
        $max = $question->get_high();
        $question_name = $this->get_complex_content_object_question()->get_id() . '_0';

        for($i = $min; $i <= $max; $i ++)
        {
            $scores[$i] = $i;
        }

        $element_template = array();
        $element_template[] = '<div><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);

        $formvalidator->addElement('select', $question_name, Translation :: get('Rating') . ': ', $scores, 'class="rating_slider"');
        $renderer->setElementTemplate($element_template, $question_name);
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'repository/content_object/assessment_rating_question/resources/javascript/assessment_rating_question.js'));
    }

    function add_borders()
    {
        return true;
    }

    function get_instruction()
    {
        $instruction = array();
        $question = $this->get_question();

        if ($question->has_description())
        {
            $instruction[] = '<div class="splitter">';
            $instruction[] = Translation :: get('SelectCorrectRating');
            $instruction[] = '</div>';
        }
        else
        {
            $instruction = array();
        }

        return implode("\n", $instruction);
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->get_formvalidator();
        $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();

        $html[] = '<div class="splitter">' . Translation :: get('Hint') . '</div>';
        $html[] = '<div class="with_borders"><a id="' . $hint_name . '" class="button hint_button">' . Translation :: get('GetAHint') . '</a></div>';

        $footer = implode("\n", $html);
        $formvalidator->addElement('html', $footer);

        parent :: add_footer($formvalidator);
    }
}
?>