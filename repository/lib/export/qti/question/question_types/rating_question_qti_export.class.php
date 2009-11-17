<?php
/**
 * $Id: rating_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class RatingQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $question = $this->get_content_object();
        
        $high = $question->get_high();
        $low = $question->get_low();
        
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="integer">';
        $item_xml[] = '</responseDeclaration>';
        $item_xml[] = $this->get_outcome_xml();
        $item_xml[] = $this->get_interaction_xml($high, $low);
        $item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
        $item_xml[] = '</assessmentItem>';
        return parent :: create_qti_file(implode('', $item_xml));
    }

    function get_outcome_xml()
    {
        $outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">';
        $outcome_xml[] = '<defaultValue>';
        $outcome_xml[] = '<value>1.0</value>';
        $outcome_xml[] = '</defaultValue>';
        $outcome_xml[] = '</outcomeDeclaration>';
        return implode('', $outcome_xml);
    }

    function get_interaction_xml($high, $low)
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<sliderInteraction responseIdentifier="RESPONSE" lowerBound="' . $low . '" upperBound="' . $high . '" step="1">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        $interaction_xml[] = '</sliderInteraction>';
        $interaction_xml[] = '</itemBody>';
        
        return implode('', $interaction_xml);
    }
}
?>