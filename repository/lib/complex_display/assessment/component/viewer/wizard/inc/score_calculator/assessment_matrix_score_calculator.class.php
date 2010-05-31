<?php
/**
 * $Id: assessment_matrix_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class AssessmentMatrixScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();
        $options = $question->get_options();
        //$matches = $question->get_matches();


        //dump($user_answers);


        $score = 0;
        $total_weight = 0;

        if ($question->get_matrix_type() == MatrixQuestion :: MATRIX_TYPE_RADIO)
        {
            foreach ($options as $index => $option)
            {
                if ($user_answers[$index] == $option->get_matches())
                {
                    $score += $option->get_score();
                }

                $total_weight += $option->get_score();
            }
        }
        else
        {
            foreach ($options as $index => $option)
            {
                $answers = array_keys($user_answers[$index]);
                $matches = $option->get_matches();

                if ($matches == null)
                    $matches = array();

                $difference = array_diff($answers, $matches);

                if (count($difference) == 0)
                {
                    $score += $option->get_score();
                }

                $total_weight += $option->get_score();
            }
        }

        return $this->make_score_relative($score, $total_weight);
    }
}







