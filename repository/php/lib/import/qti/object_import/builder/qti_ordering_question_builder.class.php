<?php

namespace repository;

use repository\content_object\ordering_question\OrderingQuestion;
use repository\content_object\ordering_question\OrderingQuestionOption;

/**
 * Question builder for ordering questions.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiOrderingQuestionBuilder extends QtiQuestionBuilder {

    static function factory($item, $settings) {
        if (!class_exists('repository\content_object\ordering_question\OrderingQuestion') ||
                $item->has_templateDeclaration() ||
                count($item->list_interactions()) != 1) {
            return null;
        }
        $main = self::get_main_interaction($item);
        if (!$main->is_orderInteraction() ||
                count($main->list_simpleChoice()) == 0) {
            return null;
        }
        return new self($settings);
    }

    public function create_question() {
        $result = new OrderingQuestion();
        return $result;
    }

    /**
     *
     * @param ImsXmlReader $item
     */
    public function build($item) {
        $result = $this->create_question();
        $result->set_title($item->get_title());
        $result->set_description($this->get_question_text($item));

        $interaction = self::get_main_interaction($item);
        $choices = $interaction->list_simpleChoice();
        $order = 1;
        foreach ($choices as $choice) {
            $value = $this->to_html($choice);
            $option = new OrderingQuestionOption($value, $order++);
            $result->add_option($option);
        }

        return $result;
    }

}

