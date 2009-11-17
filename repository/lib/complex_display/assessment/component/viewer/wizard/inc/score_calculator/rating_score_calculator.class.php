<?php
/**
 * $Id: rating_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class RatingScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();
        
        if ($question->get_correct() == $user_answers[0])
        {
            $score = 1;
        }
        else
        {
            $score = 0;
        }
        
        return $this->make_score_relative($score, 1);
    }
}
?>