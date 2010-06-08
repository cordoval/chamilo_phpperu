<?php
/**
 * $Id: fill_in_blanks_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class FillInBlanksScoreCalculator extends ScoreCalculator{

    function calculate_score(){
        $options = $this->get_question()->get_answers();
        $user_answers = $this->get_answer();
        
        $score = 0;
        foreach($options as $option){
            if ($option->get_value() == $user_answers[$option->get_position()]){
                $score += $option->get_weight();
            }
        }
        $total_weight = $this->get_question()->get_maximum_weight();
        return $this->make_score_relative($score, $total_weight);
    }
}