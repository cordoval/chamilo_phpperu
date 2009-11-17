<?php
/**
 * $Id: select_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class SelectQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $question = $this->get_content_object();
        
        $q_answers = $question->get_options();
        foreach ($q_answers as $q_answer)
        {
            $answers[] = array('answer' => $q_answer->get_value(), 'score' => $q_answer->get_weight(), 'correct' => $q_answer->is_correct(), 'feedback' => $q_answer->get_comment());
        }
        
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        
        $cardinality = $question->get_answer_type() == 'radio' ? 'single' : 'multiple';
        
        $item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="' . $cardinality . '" baseType="identifier">';
        $item_xml[] = $this->get_response_xml($answers, $question->get_answer_type());
        $item_xml[] = '</responseDeclaration>';
        $item_xml[] = $this->get_outcome_xml($answers);
        $item_xml[] = $this->get_interaction_xml($answers, $question->get_answer_type());
        $item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
        $item_xml[] = '</assessmentItem>';
        return parent :: create_qti_file(implode('', $item_xml));
    }

    function get_response_xml($answers, $type)
    {
        $response_xml[] = '<correctResponse>';
        $mapping_xml[] = '<mapping lowerBound="0" upperBound="%s" defaultValue="0">';
        $total_weight = 0;
        
        foreach ($answers as $i => $answer)
        {
            if ($answer['correct'] == true)
            {
                $response_xml[] = '<value>c' . $i . '</value>';
                $total_weight += $answer['score'];
            }
            $mapping_xml[] = '<mapEntry mapKey="c' . $i . '" mappedValue="' . $answer['score'] . '"></mapEntry>';
        }
        $response_xml[] = '</correctResponse>';
        $mapping_xml[] = '</mapping>';
        
        if ($type == 'radio')
        {
            $response = implode('', $response_xml);
        }
        else
        {
            $response = implode('', $response_xml) . sprintf(implode('', $mapping_xml), $total_weight);
        }
        
        return $response;
    }

    function get_outcome_xml($answers)
    {
        $outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">';
        $outcome_xml[] = '<defaultValue>';
        $outcome_xml[] = '<value>0</value>';
        $outcome_xml[] = '</defaultValue>';
        $outcome_xml[] = '</outcomeDeclaration>';
        //$outcome_xml[] = '<outcomeDeclaration identifier="FEEDBACK" cardinality="single" baseType="identifier" />';
        return implode('', $outcome_xml);
    }

    function get_interaction_xml($answers, $type)
    {
        $interaction_xml[] = '<itemBody>';
        
        $maxchoices = $type == 'radio' ? 1 : 0;
        
        $interaction_xml[] = '<choiceInteraction responseIdentifier="RESPONSE" shuffle="true" maxChoices="' . $maxchoices . '">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        foreach ($answers as $i => $answer)
        {
            $interaction_xml[] = '<simpleChoice identifier="c' . $i . '" fixed="false">' . $this->include_question_images($answer['answer']);
            $interaction_xml[] = '<feedbackInline outcomeIdentifier="FEEDBACK" identifier="c' . $i . '" showHide="hide">' . $this->include_question_images($answer['comment']) . '</feedbackInline>';
            $interaction_xml[] = '</simpleChoice>';
        }
        $interaction_xml[] = '</choiceInteraction>';
        $interaction_xml[] = '</itemBody>';
        
        return implode('', $interaction_xml);
    }
}
?>