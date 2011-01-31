<?php
namespace repository\content_object\survey;

use repository\content_object\survey_multiple_choice_question\SurveyMultipleChoiceQuestion;
use common\libraries\Translation;

require_once dirname(__FILE__) . '/../survey_question_display.class.php';

class SurveyMultipleChoiceQuestionDisplay extends SurveyQuestionDisplay
{

    function add_question_form()
    {
        $formvalidator = $this->get_formvalidator();

        $complex_question = $this->get_complex_question();
        $question = $this->get_question();
        $answer = $this->get_answer();

        $options = $question->get_options();
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

        $question_id = $complex_question->get_id();

//        dump($question_id);
//        dump($answer);
        
        foreach ($options as $i => $option)
        {
            $group = array();

            if ($type == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_RADIO)
            {
                $option_name = $question_id . '_0';

                $radio_button = $formvalidator->createElement('radio', $option_name, null, null, $i);

                if ($answer)
                {

//                    $key = $answer[0];
                    $key = $answer[$option_name];
                    if ($i == $key)
                    {
                       	$formvalidator->setDefaults(array($option_name => $key));
                    }
                }

                $group[] = $radio_button;
                $group[] = $formvalidator->createElement('static', null, null, $option->get_value());
            }
            elseif ($type == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX)
            {
                $option_name = $question_id . '_' . ($i);

                $check_box = $formvalidator->createElement('checkbox', $option_name);

                if ($answer[$i] == 1)
                {
                    $formvalidator->setDefaults(array($option_name => 1));
                }

                $group[] = $check_box;
                $group[] = $formvalidator->createElement('static', null, null, $option->get_value());
            }

            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);

            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));
    }

    function add_border()
    {
        return false;
    }

    function get_instruction()
    {
        $question = $this->get_question();
        $type = $question->get_answer_type();

        if ($type == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_RADIO && $question->has_description())
        {
            $title = Translation :: get('SelectYourChoice');
        }
        elseif ($type == SurveyMultipleChoiceQuestion :: ANSWER_TYPE_CHECKBOX && $question->has_description())
        {
            $title = Translation :: get('SelectYourChoices');
        }
        else
        {
            $title = '';
        }

        return $title;
    }
}
?>