<?php
namespace repository;

use repository\content_object\survey_open_question\SurveyOpenQuestion;
use repository\ContentObject;
use common\libraries\Qti;

/**
 * Serializer for open questions.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiSurveyOpenQuestionSerializer extends QtiQuestionSerializer
{

    static function factory($question, $target_root, $directory, $manifest, $toc)
    {
        if ($question instanceof SurveyOpenQuestion)
        {
            return new self($target_root, $directory, $manifest, $toc);
        }
        else
        {
            return null;
        }
    }

    protected function has_answer_feedback($question)
    {
        return false;
    }

    protected function add_response_processing($item, $question)
    {
        return null;
    }

    protected function add_score_declaration(ImsQtiWriter $item, $question)
    {
        return null;
    }

    protected function add_response_declaration(ImsQtiWriter $item, $question)
    {
        $item->add_responseDeclaration(Qti :: RESPONSE, Qti :: CARDINALITY_SINGLE, Qti :: BASETYPE_STRING);
    }

    protected function add_interaction(ImsQtiWriter $body, ContentObject $question)
    {
        $body->add_extendedTextInteraction(Qti :: RESPONSE, 800, 10);
    }
}

?>