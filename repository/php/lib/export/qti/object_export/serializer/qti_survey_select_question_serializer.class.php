<?php
namespace repository;

use repository\content_object\survey_select_question\SurveySelectQuestion;
use repository\ContentObject;

/**
 * Serializer for Select questions
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */
class QtiSurveySelectQuestionSerializer extends QtiQuestionSerializer
{

    public static function factory($question, $target_root, $directory, $manifest, $toc)
    {
        if ($question instanceof SurveySelectQuestion)
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

    protected function add_response_processing(ImsQtiWriter $item, $question)
    {
        return null;
    }

    protected function add_score_declaration(ImsQtiWriter $item, $question)
    {
        return null;
    }

    protected function add_response_declaration(ImsQtiWriter $item, $question)
    {
        $cardinality = $question->get_answer_type() == 'radio' ? Qti :: CARDINALITY_SINGLE : Qti :: CARDINALITY_MULTIPLE;
        $declaration = $item->add_responseDeclaration(Qti :: RESPONSE, $cardinality, Qti :: BASETYPE_IDENTIFIER);
        return $declaration;
    }

    protected function add_interaction(ImsQtiWriter $body, $question)
    {
        $max_choices = $question->get_answer_type() == 'radio' ? 1 : 0;
        $label = 'display=listbox';
        $result = $body->add_choiceInteraction(Qti :: RESPONSE, $max_choices, $shuffle = true, '', '', '', $label);
        $answers = $question->get_options();
        foreach ($answers as $index => $answer)
        {
            $id = "ID_$index";
            $text = $this->translate_text($answer->get_value());
            $choice = $result->add_simpleChoice($id);
            $choice->add_flow($text);
        }
        return $result;
    }
}

?>