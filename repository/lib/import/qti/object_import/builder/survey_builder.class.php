<?php

require_once Path :: get_repository_path() . 'lib/content_object/survey/survey.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/survey_page/complex_survey_page.class.php';

/**
 * Survey builder.
 *
 * @copyright (c) 2010 University of Geneva
 * @author laurent.opprecht@unige.ch
 *
 */

//@todo: test when survey is working serializer
class SurveyBuilder extends BuilderBase{

	const CLASS_DESCRIPTION = 'description';
	const CLASS_HEADER = 'header';
	const CLASS_FOOTER = 'footer';

	public static function factory($item, $settings){
		if(	!class_exists('Survey') ||
			!$item->is_assessmentTest()){
			return null;
		}

		$refs = $item->all_assessmentItemRef();
		foreach($refs as $ref){
        	$file = $ref->href;
        	$directory = $settings->get_directory();
        	if(file_exists($directory.$file)){
	        	$question = new ImsQtiReader($directory.$file, false);
	        	if(self::has_score($question)){
	        		return null;
	        	}
        	}
		}

		return new self($settings);
	}

    function build(ImsQtiReader $item){
        $survey = new Survey();
        $survey->set_title($item->title);
        $survey->set_owner_id($this->get_user()->get_id());
        $survey->set_parent_id($this->get_category());
        $survey->set_anonymous(true);
        $survey->create();
        $test_parts = $item->list_testPart();
        foreach ($test_parts as $test_part){
			$this->import_testpart($test_part, $survey);
        }
        $survey->update();
        return $survey;
    }

    function import_testpart($part, $survey){
        $sections = $part->list_assessmentSection();
        foreach ($sections as $section){
			$this->import_assessment_section($section, $survey);
		}
    }

    function import_assessment_section($section, $survey){
    	$description = '';
		$blocks = $section->list_rubricBlock();
		foreach($blocks as $block){
			//@todo: change serializer
			if($block->class == 'description'){
				$description = $this->to_html($block);
			}
		}
		if(!empty($description)){
        	$survey->set_description($description);
		}
		$this->import_assessment_subsection($section, null, $survey);
    }

    function import_assessment_subsection($section, $page, $survey){
        $refs = $section->list_assessmentItemRef();
        if(count($refs)!=0 && is_null($page)){
			$page = new SurveyPage();
			$page->set_title($section->title);
			$page->set_description('');
			$page->set_introduction_text('');
			$page->set_finish_text('');
        	$page->set_owner_id($this->get_user()->get_id());
        	$page->set_parent_id($this->get_category());

			$blocks = $section->list_rubricBlock();
			foreach($blocks as $block){
				if($block->class == self::CLASS_DESCRIPTION){
					$html = $this->to_html($block);
					$page->set_description($html);
				}else if($block->class == self::CLASS_HEADER){
					$html = $this->to_html($block);
					$page->set_introduction_text($html);
				}else if($block->class == self::CLASS_FOOTER){
					$html = $this->to_html($block);
					$page->set_finish_text($html);
				}
        	}
        	$page->create();

	        $page_co = new ComplexSurveyPage();
	        $page_co->set_ref($page->get_id());
	        $page_co->set_parent($survey->get_id());
	        $page_co->set_user_id($this->get_user()->get_id());
	        $page_co->create();
        }
        foreach($refs as $ref){
			$this->import_assessment_item_ref($ref, $page);
		}
		$subsections = $section->list_assessmentSection();
		foreach($subsections as $subsection){
			$this->import_assessment_subsection($subsection, $page, $survey);
        }
    }

    function import_assessment_item_ref($item_ref, $page){
        $file = $item_ref->href;
        $dir = $this->get_directory();
        $item_settings = $this->get_settings()->copy($dir.$file);
        $question = QtiImport::object_factory($item_settings)->import_content_object();
        if($this->accept_question($question)){
            $this->create_complex_question($page, $question);
        }else{
        	$message = Translation::get_instance()->translate('UnableToAttachObject') . ': ' . @$question->get_title();
        	$this->log_error($message);
        }
    }

    function create_complex_question($page, $question){
        $type = $question->get_type();
        require_once Path::get_repository_path() . "lib/content_object/$type/complex_$type.class.php";
        $complextype = Utilities::underscores_to_camelcase("complex_$type");
        $question_co = new $complextype();
        $question_co->set_ref($question->get_id());
        $question_co->set_parent($page->get_id());
        $question_co->set_visible(true);
        $question_co->set_user_id($this->get_user()->get_id());
        $result = $question_co->create();
        return $result;
    }


    /**
     * Returns true if the question can be added to the assessment. False otherwise.
     *
     * Note that questions could be either assessment or survey questions.
     *
     * @param $question
     */
    protected function accept_question($question){
    	if(empty($question)) return false;

    	$types = Survey::get_allowed_types();
    	foreach($types as $type){
    		if($type == $question->get_type_name()){
    			return true;
    		}
    	}
    	return false;
    }
}

?>