<?php
namespace repository\content_object\assessment;

use common\libraries\Theme;

use common\libraries\Translation;
use repository\content_object\assessment_multiple_choice_question\AssessmentMultipleChoiceQuestion;

/**
 * $Id: assessment_multiple_choice_question_result_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.question_result_display
 */
require_once dirname(__FILE__) . '/../question_result_display.class.php';

class AssessmentMultipleChoiceQuestionResultDisplay extends QuestionResultDisplay
{

    function get_question_result()
    {
        $question = $this->get_question();

        $html = array();
        $html[] = '<table class="data_table take_assessment">';
        $html[] = '<thead>';
        $html[] = '<tr>';
        $html[] = '<th class="list"></th>';
        $html[] = '<th class="list">' . Translation :: get('Answer') . '</th>';

        if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback() && $question->has_feedback())
        {
            $html[] = '<th class="list">' . Translation :: get('Feedback') . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $answers = $this->get_answers();
        $options = $question->get_options();
        $type = $question->get_answer_type();

        foreach ($options as $i => $option)
        {
            $html[] = '<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">';

            if ($type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
            {
                if (in_array($i, $answers))
                {
                    $selected = ' checked ';

                    if ($option->is_correct())
                    {
                        $result = '<img src="' . Theme :: get_image_path() . 'answer_correct.png" alt="' . Translation :: get('Correct') . '" title="' . Translation :: get('Correct') . '" style="" />';
                    }
                    else
                    {
                        $result = '<img src="' . Theme :: get_image_path() . 'answer_wrong.png" alt="' . Translation :: get('Wrong') . '" title="' . Translation :: get('Wrong') . '" />';
                    }
                }
                else
                {
                    $selected = '';

                    if ($option->is_correct())
                    {
                        $result = '<img src="' . Theme :: get_image_path() . 'answer_correct.png" alt="' . Translation :: get('Correct') . '" title="' . Translation :: get('Correct') . '" />';
                    }
                    else
                    {
                        $result = '';
                    }
                }

                $html[] = '<td><input type="radio" name="yourchoice_' . $this->get_complex_content_object_question()->get_id() . '" value="' . $i . '" disabled' . $selected . '/>' . $result . '</td>';
            }
            else
            {
                if (array_key_exists($i + 1, $answers))
                {
                    $selected = ' checked ';
                }
                else
                {
                    $selected = '';
                }

                $html[] = '<td><input type="checkbox" name="yourchoice' . $i . '" disabled' . $selected . '/></td>';
            }

            //            if ($option->is_correct())
            //            {
            //                $selected = " checked ";
            //            }
            //            else
            //            {
            //                $selected = "";
            //            }
            //            if ($type == AssessmentMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
            //            {
            //                $html[] = '<td>' . '<input type="radio" name="correctchoice_' . $this->get_complex_content_object_question()->get_id() . '" value="' . $i . '" disabled' . $selected . '/>' . '</td>';
            //            }
            //            else
            //            {
            //                $html[] = '<td>' . '<input type="checkbox" name="correctchoice_' . $i . '" disabled' . $selected . '/>' . '</td>';
            //            }


            $html[] = '<td>' . $option->get_value() . '</td>';

            if ($this->get_assessment_result_processor()->get_assessment_viewer()->display_textual_feedback() && ($option->has_feedback() || $question->has_feedback()))
            {
                $html[] = '<td>' . $option->get_feedback() . '</td>';
            }

            $html[] = '</tr>';
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        return implode("\n", $html);
    }
}
?>