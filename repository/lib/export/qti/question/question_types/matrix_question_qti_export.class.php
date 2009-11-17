<?php
/**
 * $Id: matrix_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class MatrixQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $question = $this->get_content_object();
        $options = $question->get_options();
        $matches = $question->get_matches();
        
        $item_xml = array();
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directPair">';
        $item_xml[] = $this->get_response_xml($options);
        $item_xml[] = '</responseDeclaration>';
        $item_xml[] = $this->get_outcome_xml();
        $item_xml[] = $this->get_interaction_xml($options, $matches);
        $item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
        $item_xml[] = '</assessmentItem>';
        
        $xml = implode("", $item_xml);
        
        return parent :: create_qti_file($xml);
    }

    function get_response_xml($answers)
    {
        $response_xml = array();
        $mapping_xml = array();
        
        $response_xml[] = '<correctResponse>';
        $mapping_xml[] = '<mapping defaultValue="0">';
        
        foreach ($answers as $i => $answer)
        {
            $matches = $answer->get_matches();
            
            if (! is_array($matches))
                $matches = array($matches);
            
            foreach ($matches as $match)
            {
                $response_xml[] = '<value>o' . $i . ' m' . $match . '</value>';
                $mapping_xml[] = '<mapEntry mapKey="o' . $i . ' m' . $match . '" mappedValue="' . $answer->get_weight() . '" />';
            }
        }
        
        $response_xml[] = '</correctResponse>';
        $mapping_xml[] = '</mapping>';
        
        $response = implode('', $response_xml) . implode('', $mapping_xml);
        
        return $response;
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

    function get_interaction_xml($answers, $matches)
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<matchInteraction responseIdentifier="RESPONSE" shuffle="true" maxAssociations="0">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        
        $interaction_xml[] = '<simpleMatchSet>';
        
        foreach ($answers as $i => $answer)
        {
            $interaction_xml[] = '<simpleAssociableChoice identifier="o' . $i . '" matchMax="0">' . $answer->get_value();
            $interaction_xml[] = '<feedbackInline outcomeIdentifier="FEEDBACK' . $i . '" identifier="INCORRECT" showHide="hide">' . $this->include_question_images($answer->get_comment()) . '</feedbackInline>';
            $interaction_xml[] = '</simpleAssociableChoice>';
        }
        
        $interaction_xml[] = '</simpleMatchSet>';
        $interaction_xml[] = '<simpleMatchSet>';
        
        foreach ($matches as $i => $match)
        {
            $interaction_xml[] = '<simpleAssociableChoice identifier="m' . $i . '" matchMax="0">' . $match . '</simpleAssociableChoice>';
        }
        
        $interaction_xml[] = '</simpleMatchSet>';
        $interaction_xml[] = '</matchInteraction>';
        $interaction_xml[] = '</itemBody>';
        return implode('', $interaction_xml);
    }
}
?>