<?php

namespace repository;

use repository\content_object\assessment_match_numeric_question\AssessmentMatchNumericQuestion;
use repository\content_object\assessment_match_numeric_question\AssessmentMatchNumericQuestionOption;
use common\libraries\Qti;

/**
 * Question builder for match numeric questions.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiAssessmentMatchNumericQuestionBuilder extends QtiQuestionBuilder {

    static function factory($item, $settings) {
        if (!class_exists('repository\content_object\assessment_match_numeric_question\AssessmentMatchNumericQuestion') ||
                $item->has_templateDeclaration() ||
                count($item->list_interactions()) > 1 ||
                !self::has_score($item)) {
            return null;
        }
        $main = self::get_main_interaction($item);
        if (!self::is_numeric_interaction($item, $main) ||
                !self::has_answers($item, $main)) {
            return null;
        }
        return new self($settings);
    }

    public function create_question() {
        $result = new AssessmentMatchNumericQuestion();
        return $result;
    }

    protected function get_answer($item, $answer) {
        if ($this->is_formula($answer)) {
            return $this->execute_formula($item, $answer);
        } else {
            return $answer;
        }
    }

    public function build(ImsXmlReader $item) {
        $result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));
        $interaction = self::get_main_interaction($item);
        $answers = $this->get_possible_responses($item, $interaction);

        foreach ($answers as $answer) {
            $value = $this->get_answer($item, $answer);
            $score = $this->get_score($item, $interaction, $answer);
            $tolerance = $this->get_tolerance($item, $interaction, $answer);
            $tolerance_type = $this->get_tolerance_type($item, $interaction, $answer);
            //if($tolerance_type == Qti::TOLERANCE_MODE_RELATIVE){
            //	$tolerance = $tolerance / 100 * $value;
            //}
            $feedback = $this->get_feedback($item, $interaction, $answer);
            $option = new AssessmentMatchNumericQuestionOption($value, $tolerance, $score, $feedback);
            $result->add_option($option);
        }
        $result->set_tolerance_type($this->get_question_tolerance_type($item));
        return $result;
    }

    protected function get_question_tolerance_type($item) {
        $interaction = self::get_main_interaction($item);
        $answers = $this->get_possible_responses($item, $interaction);
        foreach ($answers as $answer) {
            $tolerance_type = $this->get_tolerance_type($item, $interaction, $answer);
            if ($tolerance_type != Qti::TOLERANCE_MODE_RELATIVE) {
                return AssessmentMatchNumericQuestion::TOLERANCE_TYPE_ABSOLUTE;
            }
        }
        return AssessmentMatchNumericQuestion::TOLERANCE_TYPE_RELATIVE;
    }

}

