<?php
/**
 * $Id: hotspot_question_qti_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_export.class.php';

class HotspotQuestionQtiExport extends QuestionQtiExport
{

    function export_content_object()
    {
        $question = $this->get_content_object();
        $answers = $question->get_answers();
        
        $item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q' . $question->get_id() . '" title="' . $question->get_title() . '" adaptive="false" timeDependent="false">';
        $item_xml[] = $this->get_response_xml($answers);
        $item_xml[] = $this->get_outcome_xml();
        $item_xml[] = $this->get_interaction_xml($answers);
        //$item_xml[] = $this->get_response_processing_xml($answers);
        $item_xml[] = '</assessmentItem>';
        $file = parent :: create_qti_file(implode('', $item_xml));
        
        return $file;
    }

    function get_outcome_xml()
    {
        $outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer"/>';
        
        return implode('', $outcome_xml);
    }

    function get_response_xml($answers)
    {
        $response_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="ordered" baseType="identifier">';
        $response_xml[] = '<correctResponse>';
        foreach ($answers as $i => $answer)
        {
            $response_xml[] = '<value>A' . $i . '</value>';
        }
        $response_xml[] = '</correctResponse>';
        $response_xml[] = '</responseDeclaration>';
        
        return implode('', $response_xml);
    }

    function get_interaction_xml($answers)
    {
        $interaction_xml[] = '<itemBody>';
        //add answers 
        

        $interaction_xml[] = '<graphicOrderInteraction responseIdentifier="RESPONSE" >';
        $interaction_xml[] = '<prompt>';
        $interaction_xml[] = '<p>' . $this->include_question_images($this->get_content_object()->get_description()) . '</p>';
        $interaction_xml[] = '</prompt>';
        
        $image = $this->get_content_object()->get_image();
        $image_object = RepositoryDataManager :: get_instance()->retrieve_content_object($image);
        
        $temp_dir = Path :: get(SYS_TEMP_PATH) . $this->get_content_object()->get_owner_id() . '/export_qti/images/' . $image_object->get_filename();
        mkdir(Path :: get(SYS_TEMP_PATH) . $this->get_content_object()->get_owner_id() . '/export_qti/images/', null, true);
        copy(Path :: get(SYS_FILE_PATH) . 'repository/' . $image_object->get_path(), $temp_dir);
        
        $extension = split('.', $image_object->get_filename());
        $extension = $extension[0];
        
        //$interaction_xml[] = '<object type="image/'.$extension.'" width="'.$size[0].'" height="'.$size[1].'" data="images/'.$image_object->get_filename().'"></object>';
        $interaction_xml[] = '<object type="image/' . $extension . '" data="images/' . $image_object->get_filename() . '"></object>';
        
        $firstcoords = null;
        
        foreach ($answers as $i => $answer)
        {
            $coords = unserialize($answer->get_hotspot_coordinates());
            $firstcoords = $coords[0];
            
            $export_coords = array();
            
            foreach ($coords as $coord)
            {
                $export_coords[] = implode(", ", $coord);
            }
            
            $export_coords[] = implode(", ", $firstcoords);
            $export_coords = implode(", ", $export_coords);
            
            //$type = $answer->get_hotspot_type();
            //$export_type = $this->export_type($type);
            //$export_coords = $this->transform_coords($coords, $export_type);
            

            $interaction_xml[] = '<hotspotChoice shape="poly" coords="' . $export_coords . '" identifier="A' . $i . '">';
            $interaction_xml[] = '<feedbackInline outcomeIdentifier="FEEDBACK' . $i . '" identifier="INCORRECT" showHide="hide">' . $this->include_question_images($answer->get_comment()) . '</feedbackInline>';
            $interaction_xml[] = '</hotspotChoice>';
        }
        $interaction_xml[] = '</graphicOrderInteraction>';
        
        $interaction_xml[] = '</itemBody>';
        return implode('', $interaction_xml);
    }

    function export_type($type)
    {
        switch ($type)
        {
            case 'square' :
                return 'rect';
            case 'circle' :
                return 'ellipse';
            case 'poly' :
                return 'poly';
            default :
                return '';
        }
    }

    function transform_coords($coords, $export_type)
    {
        switch ($export_type)
        {
            case 'rect' :
                $coords = str_replace('|', ',', $coords);
                $coords = str_replace(';', ',', $coords);
                $parts = split(',', $coords);
                $points = $parts[0] . ',' . $parts[1] . ',' . ($parts[2] + $parts[0]) . ',' . ($parts[3] + $parts[1]);
                return $points;
            case 'ellipse' :
                $coords = str_replace('|', ',', $coords);
                $coords = str_replace(';', ',', $coords);
                return $coords;
            case 'poly' :
                $coords = str_replace('|', ',', $coords);
                $coords = str_replace(';', ',', $coords);
                $parts = split(',', $coords);
                $coords .= ',' . $parts[0] . ',' . $parts[1];
                return $coords;
            default :
                return '';
        }
    }
}
?>