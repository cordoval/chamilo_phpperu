<?php
/**
 * $Id: ordering_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class OrderingQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $question = $this->get_content_object();
        $answers = $question->get_options();
        
        $item_xml = array();
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="ordered" baseType="identifier">';
        $item_xml[] = $this->get_response_xml($answers);
        $item_xml[] = '</responseDeclaration>';
        $item_xml[] = $this->get_outcome_xml();
        $item_xml[] = $this->get_interaction_xml($answers);
        $item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
        $item_xml[] = '</assessmentItem>';
        
        $xml = implode("", $item_xml);
        
        return parent :: create_qti_file($xml);
    }

    function get_response_xml($answers)
    {
        $ordered_answers = $this->order_answers($answers);
        
        $response_xml = array();
        $response_xml[] = '<correctResponse>';
        
        foreach ($ordered_answers as $i => $order)
        {
            $response_xml[] = '<value>c' . $i . '</value>';
        }
        
        $response_xml[] = '</correctResponse>';
        
        return implode('', $response_xml);
    }

    function get_outcome_xml()
    {
        $outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">';
        $outcome_xml[] = '<defaultValue>';
        $outcome_xml[] = '<value>0</value>';
        $outcome_xml[] = '</defaultValue>';
        $outcome_xml[] = '</outcomeDeclaration>';
        return implode('', $outcome_xml);
    }

    function get_interaction_xml($answers)
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<orderInteraction responseIdentifier="RESPONSE" shuffle="true">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        
        foreach ($answers as $i => $answer)
        {
            $interaction_xml[] = '<simpleChoice identifier="c' . $i . '">' . htmlspecialchars($answer->get_value()) . '</simpleChoice>';
        }
        
        $interaction_xml[] = '</orderInteraction>';
        $interaction_xml[] = '</itemBody>';
        return implode('', $interaction_xml);
    }

    function order_answers($answers)
    {
        $ordered_answers = array();
        
        foreach ($answers as $answer)
        {
            $ordered_answers[] = $answer->get_order();
        }
        
        asort($ordered_answers);
        
        return $ordered_answers;
    }
}
?>