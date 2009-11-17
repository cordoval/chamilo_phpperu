<?php
/**
 * $Id: hotspot_question_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question.question_types
 */
require_once dirname(__FILE__) . '/../question_qti_import.class.php';

class HotspotQuestionQtiImport extends QuestionQtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        
        $question = new HotspotQuestion();
        $title = $data['title'];
        
        $interaction = $data['itemBody']['graphicOrderInteraction'];
        $description = parent :: get_tag_content('prompt');
        $description = parent :: import_images($description);
        
        $question->set_title($title);
        $question->set_description($description);
        $image = $interaction['object']['data'];
        $parts = split('/', $image);
        $imagename = $parts[count($parts) - 1];
        
        $new_dir = Path :: get(SYS_PATH) . 'files/repository/' . $this->get_user()->get_id();
        $orig_path = dirname($this->get_content_object_file()) . '/' . $image;
        $new_filename = Filesystem :: create_unique_name($new_dir, $imagename);
        
        copy($orig_path, $new_dir . '/' . $new_filename);
        
        $object = new Document();
        $object->set_title($new_filename);
        $object->set_description($new_filename);
        $object->set_path($this->get_user()->get_id() . '/' . $new_filename);
        $object->set_filename($new_filename);
        $object->set_filesize(Filesystem :: get_disk_space($new_dir . '/' . $new_filename));
        $object->set_owner_id($this->get_user()->get_id());
        $object->set_parent_id(0);
        $object->create();
        
        $question->set_image($object->get_id());
        
        $this->create_answers($question, $interaction['hotspotChoice']);
        parent :: create_question($question);
        return $question->get_id();
    }

    function create_answers($question, $answers)
    {
        foreach ($answers as $i => $answer)
        {
            $type = $answer['shape'];
            
            if ($type != 'poly')
                continue;
            
            $coords = $answer['coords'];
            $answer_text = 'import' . $i;
            
            $hotspot_type = $this->convert_type($type);
            $hotspot_coords = $this->convert_coords($hotspot_type, $coords);
            $hotspot_answer = new HotspotQuestionAnswer($answer_text, '', 1, serialize($hotspot_coords));
            $question->add_answer($hotspot_answer);
        }
    }

    function convert_type($type)
    {
        switch ($type)
        {
            case 'rect' :
                return 'square';
            case 'ellipse' :
                return 'circle';
            case 'circle' :
                return 'circle';
            case 'poly' :
                return 'poly';
            default :
                return '';
        }
    }

    function convert_coords($type, $coords)
    {
        switch ($type)
        {
            case 'square' :
                $points = split(',', $coords);
                $hotspot_coords = $points[0] . ';' . $points[1] . '|' . ($points[2] - $points[0]) . '|' . ($points[3] - $points[1]);
                //dump($hotspot_coords);
                return $hotspot_coords;
            case 'ellipse' :
                $points = split(',', $coords);
                $hotspot_coords = $points[0] . ';' . $points[1] . '|' . $points[2] . '|' . $points[3];
                return $hotspot_coords;
            case 'circle' :
                $points = split(',', $coords);
                $hotspot_coords = $points[0] . ';' . $points[1] . '|' . $points[2] . '|' . $points[2];
                return $hotspot_coords;
            case 'poly' :
                $points = split(',', $coords);
                $hotspot_coords = array();
                for($i = 0; $i < count($points) - 2; $i += 2)
                {
                    $hotspot_coords[] = array(intval(trim($points[$i])), intval(trim($points[$i + 1])));
                }
                return $hotspot_coords;
            default :
                return '';
        }
    }
}
?>