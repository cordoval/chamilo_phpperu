<?php

namespace repository;

use repository\content_object\match_question\MatchQuestion;
use repository\content_object\match_question\MatchQuestionOption;

/**
 * Question builder for match questions.
 * Only accept match questions that have been exported by Chamilo.
 * Other questions will go to MatchTextQuestion.
 *
 * Note that MatchTextQuestion with UseWildcards = false and IgnoreCase = false provides the same functionalities as match question
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiMatchQuestionBuilder extends QtiQuestionBuilder {

    static function factory($item, $settings) {
        if (!class_exists('repository\content_object\match_question\MatchQuestion') ||
                $item->has_templateDeclaration() ||
                count($item->list_interactions()) != 1 ||
                !self::has_score($item)) {
            return null;
        }

        $main = self::get_main_interaction($item);
        $is_text_entry = $main->is_extendedTextInteraction() || $main->is_textEntryInteraction();
        $is_numeric = self::is_numeric_interaction($item, $main);
        $has_answers = self::has_answers($item, $main);
        if (!$is_text_entry || $is_numeric || !$has_answers) {
            return null;
        }

        //only accept questions that have been exported by Chamilo
        if ($item->toolName == self::get_tool_name()) {
            $label = $main->label;
            $pairs = explode(';', $label);
            foreach ($pairs as $pair) {
                $entry = explode('=', $pair);
                if (count($entry) == 2) {
                    $key = reset($entry);
                    $value = trim($entry[1]);
                    if ($key == 'display' && $value != 'MatchQuestion') {
                        return false;
                    }
                }
            }
        }

        return new self($settings);
    }

    public function create_question() {
        $result = new MatchQuestion();
        return $result;
    }

    public function build(ImsXmlReader $item) {
        $result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));


        $interaction = self::get_main_interaction($item);
        $answers = $this->get_possible_responses($item, $interaction);
        foreach ($answers as $answer) {

            $value = $this->get_response_text($item, $answer);
            $score = $this->get_score($item, $interaction, $answer);
            $feedback = $this->get_feedback($item, $interaction, $answer);
            $option = new MatchQuestionOption($value, $score, $feedback);
            $result->add_option($option);
        }
        return $result;
    }

    protected function get_response_text($item, $response) {
        if (!$response instanceof ImsXmlReader) {
            $result = $response;
        } else {
            $result = $this->execute_formula($item, $response);
        }

        return $result;
    }

}
