<?php
/**
 * $Id: assessment_qti_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.assessment
 */
require_once Path :: get_plugin_path() . 'pear/XML/Unserializer.php';
require_once Path :: get_repository_path() . 'lib/content_object/assessment/assessment.class.php';

class AssessmentQtiImport extends QtiImport
{

    function import_content_object()
    {
        $data = $this->get_file_content_array();
        
        $assessment = new Assessment();
        $title = $data['title'];
        $assessment->set_title($title);
        $assessment->set_owner_id($this->get_user()->get_id());
        $assessment->set_parent_id(0);
        $assessment->set_assessment_type(Assessment :: TYPE_EXERCISE);
        //dump($assessment);
        $assessment->create();
        
        $testparts = $data['testPart'];
        
        if ($testparts[0] != null)
        {
            foreach ($testparts as $testpart)
            {
                $this->import_testpart($testpart, $assessment);
            }
        }
        else
        {
            $this->import_testpart($testparts, $assessment);
        }
        return $assessment;
    }

    function import_testpart($part, $assessment)
    {
        $assessment_sections = $part['assessmentSection'];
        $max_times_taken = $part['itemSessionControl']['maxAttempts'];
        if ($max_times_taken != null)
        {
            $assessment->set_maximum_attempts($max_times_taken);
            $assessment->update();
        }
        if ($assessment_sections[0] != null)
        {
            foreach ($assessment_sections as $section)
            {
                $this->import_assessment_section($section, $assessment);
            }
        }
        else
        {
            $this->import_assessment_section($assessment_sections, $assessment);
        }
    }

    function import_assessment_section($assessment_section, $assessment)
    {
        //echo 'hier3';
        $descr = $assessment_section['title'];
        $assessment->set_description($this->import_images($descr));
        
        $assessment->update();
        $assessment_item_refs = $assessment_section['assessmentItemRef'];
        if ($assessment_item_refs[0] != null)
        {
            foreach ($assessment_item_refs as $item_ref)
            {
                $this->import_assessment_item_ref($item_ref, $assessment);
            }
        }
        else
        {
            $this->import_assessment_item_ref($assessment_item_refs, $assessment);
        }
    }

    function import_assessment_item_ref($item_ref, $assessment)
    {
        //echo 'hier2';
        $item_ref_file = $item_ref['href'];
        $weight = $item_ref['weight']['value'];
        $dirparts = split('/', $this->get_content_object_file());
        for($i = 0; $i < count($dirparts) - 1; $i ++)
        {
            $dir .= $dirparts[$i] . '/';
        }
        //dump($item_ref_file);
        $question_qti_import = QtiImport :: factory_qti($item_ref_file, $this->get_user(), $this->get_category(), $dir);
        $qid = $question_qti_import->import_content_object();
        
        if ($qid != null)
        {
            $question = RepositoryDataManager :: get_instance()->retrieve_content_object($qid);
            //dump($question);
            $this->create_complex_question($assessment, $question, $weight);
        }
    }

    function create_complex_question($assessment, $question, $weight)
    {
        //echo 'hier';
        $type = $question->get_type();
        $complextype = 'complex_' . $type;
        require_once Path :: get_repository_path() . 'lib/content_object/' . $type . '/' . $complextype . '.class.php';
        $complextypecc = Utilities :: underscores_to_camelcase($complextype);
        $question_clo = new $complextypecc();
        $question_clo->set_ref($question->get_id());
        $question_clo->set_parent($assessment->get_id());
        $question_clo->set_weight($weight);
        $question_clo->set_user_id($this->get_user()->get_id());
        //dump($question_clo);
        return $question_clo->create();
    }

    function import_images($text)
    {
        $tags = Text :: fetch_tag_into_array($text, '<img>');
        $new_dir = Path :: get(REL_PATH) . 'files/repository/' . $this->get_user()->get_id() . '/';
        
        if (! file_exists($temp_dir))
        {
            mkdir($temp_dir, null, true);
        }
        
        foreach ($tags as $tag)
        {
            $parts = split('/', $tag['src']);
            $newfilename = $new_dir . $parts[count($parts) - 1];
            //$repl_filename = $new.$parts[count($parts)-1];
            $files[$newfilename] = $tag['src']; //str_replace($base_path, '', $tag['src']);
            $text = str_replace($tag['src'], $newfilename, $text);
        }
        //dump(htmlspecialchars($description));
        //dump($files);
        $orig_path = dirname($this->get_content_object_file());
        foreach ($files as $new => $original)
        {
            //dump($orig_path.'/'.$original);
            //dump($new);
            copy($orig_path . '/' . $original, $new);
        }
        //dump($text);
        return $text;
    }
}
?>