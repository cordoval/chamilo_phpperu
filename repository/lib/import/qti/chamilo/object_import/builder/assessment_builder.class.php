<?php

require_once Path :: get_repository_path() . 'lib/content_object/assessment/assessment.class.php';

/**
 * Assessment builder.
 * 
 * University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentBuilder extends BuilderBase{
	
	public static function factory($item, $source_root, $target_root, $category, $user, $object_factory, $log){
		if(	!class_exists('Assessment') ||
			!$item->is_assessmentTest()){
			return null;
		}
		$refs = $item->all_assessmentItemRef();
		foreach($refs as $ref){
        	$file = $ref->href;
        	$question = new ImsQtiReader("$source_root/$file", false);
        	if(self::has_score($question)){
        		return new self($source_root, $target_root, $category, $user, $object_factory, $log);
        	}
		}
		return null;
	}
	
    function build(ImsQtiReader $item){
        $assessment = new Assessment();
        $assessment->set_title($item->title);
        $assessment->set_owner_id($this->get_user()->get_id());
        $assessment->set_parent_id($this->get_category());
        $assessment->set_assessment_type(Assessment::TYPE_EXERCISE);
        $assessment->create(); 
        
        $test_parts = $item->list_testPart();
        foreach ($test_parts as $test_part){
			$this->import_testpart($test_part, $assessment);
        }
        $assessment->update();
        return $assessment;
    }

    function import_testpart($part, $assessment){
        $maximum_attempts = $part->get_itemSessionControl()->maxAttempts;
        //@todo: update serializer
        if(!empty($maximum_attempts)){
            $assessment->set_maximum_attempts($maximum_attempts);
        }
        $sections = $part->list_assessmentSection();
        foreach ($sections as $section){
			$this->import_assessment_section($section, $assessment);
		}
    }

    function import_assessment_section($section, $assessment){
		$description = $section->get_rubricBlock();
		$description = $this->to_html($description);
        $assessment->set_description($description);
        
        $refs = $section->list_assessmentItemRef();
        foreach($refs as $ref){
			$this->import_assessment_item_ref($ref, $assessment);
		}
    }

    function import_assessment_item_ref($item_ref, $assessment){
        $file = $item_ref->href;
        $dir = $this->get_source_root();
        if($id = $this->get_object_factory()->import($dir.$file, $this->get_user(), $this->get_category(), $this->get_log())){
        	$question = RepositoryDataManager::get_instance()->retrieve_content_object($id);
        	$weight = $item_ref->get_weight()->value;
            $this->create_complex_question($assessment, $question, $weight);
        }
    }

    function create_complex_question($assessment, $question, $weight){
        $type = $question->get_type();
        require_once Path::get_repository_path() . "lib/content_object/$type/complex_$type.class.php";
        $complextype = Utilities::underscores_to_camelcase("complex_$type");
        $question_co = new $complextype();
        $question_co->set_ref($question->get_id());
        $question_co->set_parent($assessment->get_id());
        $question_co->set_weight($weight);
        $question_co->set_user_id($this->get_user()->get_id());
        $result = $question_co->create();
        return $result;
    }
}
?>