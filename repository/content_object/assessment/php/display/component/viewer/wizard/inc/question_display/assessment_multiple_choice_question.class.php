<?php
namespace repository\content_object\assessment;

use common\libraries\Path;
use common\libraries\ResourceManager;
use common\libraries\Translation;
use repository\content_object\assessment_multiple_choice_question\AssessmentMultipleChoiceQuestion;

/**
 * $Id: multiple_choice_question.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_display
 */
require_once dirname(__FILE__) . '/../question_display.class.php';

class AssessmentMultipleChoiceQuestionDisplay extends QuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();
        $clo_question = $this->get_complex_content_object_question();
        $question = $this->get_question();
        $answers = $this->shuffle_with_keys($question->get_options());
        $type = $question->get_answer_type();
        $renderer = $this->get_renderer();

        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));

        $question_id = $clo_question->get_id();

        foreach ($answers as $i => $answer)
        {
            $group = array();

            if ($type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
            {
                $answer_name = $question_id . '_0';
                $group[] = $formvalidator->createElement('radio', $answer_name, null, null, $i);
                $group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
            }
            elseif ($type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
            {
                $answer_name = $question_id . '_' . ($i + 1);
                $group[] = $formvalidator->createElement('checkbox', $answer_name);
                $group[] = $formvalidator->createElement('static', null, null, $answer->get_value());
            }

            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);

            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
        $formvalidator->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get_repository_content_object_path(true) . 'assessment/resources/javascript/hint.js'));
    }

    function add_border()
    {
        return false;
    }

    function get_instruction()
    {
        $question = $this->get_question();
        $type = $question->get_answer_type();

        if ($type == 'radio' && $question->has_description())
        {
            $title = Translation :: get('SelectCorrectAnswer');
        }
        elseif ($type == 'checkbox' && $question->has_description())
        {
            $title = Translation :: get('SelectCorrectAnswers');
        }
        else
        {
            $title = '';
        }

        return $title;
    }

    function add_footer($formvalidator)
    {
        $formvalidator = $this->get_formvalidator();

        if ($this->get_question()->has_hint())
        {
            $hint_name = 'hint_' . $this->get_complex_content_object_question()->get_id();

            $html[] = '<div class="splitter">' . Translation :: get('Hint') . '</div>';
            $html[] = '<div class="with_borders"><a id="' . $hint_name . '" class="button hint_button">' . Translation :: get('GetAHint') . '</a></div>';

            $footer = implode("\n", $html);
            $formvalidator->addElement('html', $footer);
        }

        parent :: add_footer($formvalidator);
    }
}
?>