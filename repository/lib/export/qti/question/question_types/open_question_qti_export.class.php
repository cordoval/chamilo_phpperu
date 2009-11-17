<?php
/**
 * $Id: open_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class OpenQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $question = $this->get_content_object();
        
        $q_type = $question->get_question_type();
        switch ($q_type)
        {
            case OpenQuestion :: TYPE_OPEN :
                $item_xml = $this->get_open_item_xml($question);
                return parent :: create_qti_file($item_xml);
            case OpenQuestion :: TYPE_DOCUMENT :
                $item_xml = $this->get_document_item_xml($question);
                return parent :: create_qti_file($item_xml);
            case OpenQuestion :: TYPE_OPEN_WITH_DOCUMENT :
                $item_xml = $this->get_both_item_xml($question);
                return parent :: create_qti_file($item_xml);
        }
        
    //return parent :: create_qti_file($item_xml);
    }

    function get_open_item_xml($question)
    {
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="string" />';
        
        $item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer" />';
        $item_xml[] = $this->get_open_interaction_xml();
        $item_xml[] = '</assessmentItem>';
        return implode('', $item_xml);
    }

    function get_open_interaction_xml()
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<extendedTextInteraction responseIdentifier="RESPONSE" expectedLength="500">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        
        $interaction_xml[] = '</extendedTextInteraction>';
        $interaction_xml[] = '</itemBody>';
        
        return implode('', $interaction_xml);
    }

    function get_document_item_xml($question)
    {
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="file" />';
        
        $item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer" />';
        $item_xml[] = $this->get_document_interaction_xml();
        $item_xml[] = '</assessmentItem>';
        return implode('', $item_xml);
    }

    function get_document_interaction_xml()
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<uploadInteraction responseIdentifier="RESPONSE">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        
        $interaction_xml[] = '</uploadInteraction>';
        $interaction_xml[] = '</itemBody>';
        
        return implode('', $interaction_xml);
    }

    function get_both_item_xml($question)
    {
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE_T" cardinality="single" baseType="string" />';
        $item_xml[] = '<responseDeclaration identifier="RESPONSE_D" cardinality="single" baseType="file" />';
        $item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer" />';
        $item_xml[] = $this->get_both_interaction_xml();
        $item_xml[] = '</assessmentItem>';
        return implode('', $item_xml);
    }

    function get_both_interaction_xml()
    {
        $interaction_xml[] = '<itemBody>';
        $interaction_xml[] = '<extendedTextInteraction responseIdentifier="RESPONSE_T" expectedLength="250">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        
        $interaction_xml[] = '</extendedTextInteraction>';
        $interaction_xml[] = '<uploadInteraction responseIdentifier="RESPONSE_P">';
        $interaction_xml[] = '<prompt>' . $this->include_question_images($this->get_content_object()->get_description()) . '</prompt>';
        
        $interaction_xml[] = '</uploadInteraction>';
        $interaction_xml[] = '</itemBody>';
        
        return implode('', $interaction_xml);
    }
}
?>