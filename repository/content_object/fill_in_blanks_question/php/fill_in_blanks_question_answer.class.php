<?php

/**
 * $Id: fill_in_blanks_question_answer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.question_types.fill_in_blanks_question
 */
class FillInBlanksQuestionAnswer{
	
	const CLOZE_REGEX = '/\[[^\[\]]*\]/';
	
	/**
	 * 
	 * @param string $text formats question [answer 1(feedback 1)=score 1, answer 2=score 2, answer 3(feedback 3)] question 2 [answer 1, answer 2].
	 * @return array of question's answers
	 */
	static function parse($text){
		$result = array();
		$matches = array();
        preg_match_all(self::CLOZE_REGEX, $text, $matches);
        $index = 0;
        $matches = empty($matches) ? array() : $matches[0];
        foreach($matches as $match){
        	$match = trim($match, '[]');
        	$answers = explode(',', $match);
        	foreach($answers as $answer){
        		$parts = explode('=', $answer);
        		$score = count($parts)>1 ? $parts[1] : 1;
        		$score = is_numeric($score) ? $score : 1;
        		$parts = str_replace(')', '=', str_replace('(', '=', $parts[0]));
        		$parts = explode('=', $parts);
        		$feedback = count($parts)>1 ? $parts[1] : '';
        		$answer = $parts[0];
        		$result[] = new FillInBlanksQuestionAnswer($answer, $score, $feedback, '', $index);
        	}
        	$index++;
        }
        return $result;
	}
	
	static function format($answer){
		//@todo: if needed
	}
	
	static function get_number_of_questions($text){
		$matches = array();
		return preg_match_all(self::CLOZE_REGEX, $text, $matches);
	}
	
    private $value;
    private $weight;
    private $comment;
    private $size;
    private $position;

    function FillInBlanksQuestionAnswer($value, $weight, $comment, $size, $position){
        $this->value = $value;
        $this->weight = $weight;
        $this->comment = $comment;
        $this->size = empty($size) ? strlen($value) : $size;
        $this->position = $position;
    }

    function get_comment(){
        return $this->comment;
    }

    function get_value(){
        return $this->value;
    }

    function get_weight(){
        return $this->weight;
    }

    function get_size(){
        return $this->size;
    }

    function get_position(){
        return $this->position;
    }
}
?>