<?php

namespace repository;

use repository\content_object\survey_description\SurveyDescription;

/**
 * Question builder for Survey Description Questions.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiSurveyDescriptionBuilder extends QtiQuestionBuilder {

    static function factory($item, $settings) {
        if (!class_exists('repository\content_object\survey_description\SurveyDescription') ||
                $item->has_templateDeclaration() ||
                self::has_score($item) ||
                count($item->list_interactions()) > 0) {
            return null;
        }
        return new self($settings);
    }

    public function create_question() {
        $result = new SurveyDescription();
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
        return $result;
    }

}

