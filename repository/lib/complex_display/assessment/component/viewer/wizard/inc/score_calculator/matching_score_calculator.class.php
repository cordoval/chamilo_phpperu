<?php
/**
 * $Id: matching_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class MatchingScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        
        $answers = $this->get_question()->get_options();
        $score = 0;
        $total_weight = 0;
        
        foreach ($answers as $i => $answer)
        {
            if ($user_answers[$i] == $answer->get_match())
            {
                $score += $answer->get_weight();
            }
            
            $total_weight += $answer->get_weight();
        }
        
        return $this->make_score_relative($score, $total_weight);
    }
}
?>