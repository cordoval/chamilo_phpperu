<?php
/**
 * $Id: match_score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc.score_calculator
 */
require_once dirname(__FILE__) . '/../score_calculator.class.php';

class MatchScoreCalculator extends ScoreCalculator
{

    function calculate_score()
    {
        $user_answers = $this->get_answer();
        $question = $this->get_question();
        $options = $question->get_options();
        
        foreach ($options as $option)
        {
            if ($option->get_value() == trim($user_answers[0]))
            {
                return $this->make_score_relative($option->get_weight(), $option->get_weight());
            }
        }
        
        return 0;
    }
}
?>