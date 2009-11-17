<?php
/**
 * $Id: fill_in_blanks_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class FillInBlanksQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $question = $this->get_content_object();
        $answers = $question->get_answers();
        
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = $this->get_response_xml($answers);
        $item_xml[] = $this->get_outcome_xml();
        $item_xml[] = $this->get_interaction_xml($answers, $question->get_answer_text());
        $item_xml[] = $this->get_response_processing_xml($answers);
        $item_xml[] = '</assessmentItem>';
        $file = parent :: create_qti_file(implode('', $item_xml));
        //echo(implode('', $item_xml));
        //echo($file);
        return $file;
    }

    function get_outcome_xml()
    {
        $outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">';
        //$outcome_xml[] = '<outcomeDeclaration identifier="FEEDBACK" cardinality="single" baseType="identifier">';
        $outcome_xml[] = '<defaultValue>';
        $outcome_xml[] = '<value>0</value>';
        $outcome_xml[] = '</defaultValue>';
        $outcome_xml[] = '</outcomeDeclaration>';
        return implode('', $outcome_xml);
    }

    function get_response_xml($answers)
    {
        foreach ($answers as $i => $answer)
        {
            $value = substr($answer->get_value(), 1, strlen($answer->get_value()) - 2);
            $response_xml[] = '<responseDeclaration identifier="c' . $i . '" cardinality="single" baseType="string">';
            $response_xml[] = '<correctResponse>';
            $response_xml[] = '<value>' . htmlspecialchars($value) . '</value>';
            $response_xml[] = '<mapping defaultValue="0">';
            $response_xml[] = '<mapEntry mapKey="' . htmlspecialchars($value) . '" mappedValue="' . $answer->get_weight() . '"/>';
            $response_xml[] = '</mapping>';
            $response_xml[] = '</correctResponse>';
            $response_xml[] = '</responseDeclaration>';
        }
        return implode('', $response_xml);
    }

    function get_interaction_xml($answers, $answer_text)
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        foreach ($answers as $i => $answer)
        {
            $pos = strpos($answer_text, $answer->get_value());
            $interaction_xml[] = substr($answer_text, 0, $pos);
            $start = $pos + strlen($answer->get_value());
            $answer_text = substr($answer_text, $start, strlen($answer_text) - $start);
            
            $interaction_xml[] = '<textEntryInteraction responseIdentifier="c' . $i . '" expectedLength="20">';
            $interaction_xml[] = '<feedbackInline outcomeIdentifier="FEEDBACK' . $i . '" identifier="INCORRECT" showHide="hide">' . $this->include_question_images($answer->get_comment()) . '</feedbackInline>';
            $interaction_xml[] = '</textEntryInteraction>';
        }
        
        $interaction_xml[] = '</itemBody>';
        
        return implode('', $interaction_xml);
    }

    function get_response_processing_xml($answers)
    {
        $rp_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct">';
        foreach ($answers as $i => $answer)
        {
            $rp_xml[] = '<responseCondition>';
            $rp_xml[] = '<responseIf>';
            
            $rp_xml[] = '<not>';
            $rp_xml[] = '<match>';
            $rp_xml[] = '<variable identifier="c' . $i . '" />';
            $rp_xml[] = '<correct identifier="c' . $i . '" />';
            $rp_xml[] = '</match>';
            $rp_xml[] = '</not>';
            
            $rp_xml[] = '<setOutcomeValue identifier="FEEDBACK' . $i . '" >';
            $rp_xml[] = '<baseValue baseType="identifier">INCORRECT</baseValue>';
            $rp_xml[] = '</setOutcomeValue>';
            
            $rp_xml[] = '</responseIf>';
            $rp_xml[] = '</responseCondition>';
        }
        $rp_xml[] = '</responseProcessing>';
        return implode('', $rp_xml);
    }
}
?>