<?php
/**
 * $Id: score_calculator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component.viewer.wizard.inc
 */
/**
 * Abstract class so each question type can determine the correct score with the given answers
 *
 */
abstract class ScoreCalculator
{
    private $answer;
    private $question;

    function ScoreCalculator($question, $answer, $weight)
    {
        $this->answer = $answer;
        $this->question = $question;
        $this->weight = $weight;
    }

    abstract function calculate_score();

    function get_answer()
    {
        return $this->answer;
    }

    function get_question()
    {
        return $this->question;
    }

    function get_weight()
    {
        return $this->weight;
    }

    function make_score_relative($score, $total_weight)
    {
        $relative_weight = $this->weight;
        
        if ($relative_weight == null)
            return $score;
        
        $factor = ($total_weight / $relative_weight);
        $new_score = round(($score / $factor) * 100) / 100;
        
        return $new_score;
    }

    static function factory($question, $answer, $weight)
    {
        $type = $question->get_type();
        $type = str_replace('_question', '', $type);
        $file = dirname(__FILE__) . '/score_calculator/' . $type . '_score_calculator.class.php';
        
        if (! file_exists($file))
        {
            die('file does not exist: ' . $file);
        }
        
        require_once $file;
        
        $class = Utilities :: underscores_to_camelcase($type) . 'ScoreCalculator';
        $score_calculator = new $class($question, $answer, $weight);
        return $score_calculator;
    }
}
?>