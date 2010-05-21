<?php
/**
 * $Id: assessment_multiple_choice_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class AssessmentMultipleChoiceScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();

        $question = $this->get_question();
        if ($question->get_answer_type() == MultipleChoiceQuestion::ANSWER_TYPE_RADIO)
        {
            $answers = $question->get_options();
            $selected = $answers[$user_answers[0]];
            if ($selected && $selected->is_correct())
            {
                return $this->make_score_relative($selected->get_score(), $selected->get_score());
            }
            else
            {
                return 0;
            }
        }
        else
        {
            $answers = $question->get_options();
            $score = 0;
            $total_weight = 0;

            foreach ($answers as $i => $answer)
            {
                if (array_key_exists($i + 1, $user_answers) && ($answer->is_correct() || $answer->get_score() <= 0))
                {
                    $score += $answer->get_score();
                }

                if ($answer->is_correct())
                {
                    $total_weight += $answer->get_score();
                }
            }
            return $this->make_score_relative($score, $total_weight);
        }
    }
}
?>