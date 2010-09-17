<?php

/**
 * Qti serializer for survey questions. 
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class SurveySerializer extends SerializerBase{
	
	const CLASS_DESCRIPTION = 'description';
	const CLASS_HEADER = 'header';
	const CLASS_FOOTER = 'footer';
	
	static function factory($object, $target_root, $directory, $manifest, $toc){
		if($object instanceof Survey){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	public function serialize(Survey $survey){
        $writer = new ImsQtiWriter();
        $survey_id = self::get_identifier($survey);
        $test = $writer->add_assessmentTest($survey_id, $survey->get_title(), Qti::get_tool_name(), Qti::get_tool_version());
        $part = $test->add_testPart(null, Qti::NAVIGATION_MODE_LINEAR, Qti::SUBMISSION_MODE_INDIVIDUAL);
        $part->add_itemSessionControl(0, true, true, true, true, true, false);
        $section = $part->add_assessmentSection(null, $survey->get_title(), false);
        $instruction = $this->translate_text($survey->get_description());
        
        $section->add_rubricBlock(Qti::VIEW_ALL, self::CLASS_DESCRIPTION)->add_flow($instruction);
        
        $this->add_children($section, $survey->get_id());
        return $writer->saveXML();
	}
	
	protected function add_children(ImsQtiWriter $writer, $parent_id){
        $children = $this->get_children($parent_id);
        while ($child = $children->next_result()){
            $child_object = $this->retrieve_object($child->get_ref());
            if($child_object instanceof SurveyPage){
            	$section = $writer->add_assessmentSection(null, $child_object->get_title(), $visible = true);
        		$instruction = $this->translate_text($child_object->get_description());
            	$section->add_rubricBlock(Qti::VIEW_ALL, self::CLASS_DESCRIPTION)->add_flow($instruction);
            	
        		$instruction = $this->translate_text($child_object->get_introduction_text());
            	$section->add_rubricBlock(Qti::VIEW_ALL, self::CLASS_HEADER)->add_flow($instruction);
            	
        		$instruction = $this->translate_text($child_object->get_finish_text());
            	$section->add_rubricBlock(Qti::VIEW_ALL, self::CLASS_FOOTER)->add_flow($instruction);
            	
            	$this->add_children($section, $child_object->get_id());
            	
            }else if($exporter = $this->create_exporter($child_object)){
	            $path = $exporter->export_content_object();
	            $filename = basename($path);
	            $ref = $writer->add_assessmentItemRef('ID_'.$child->get_id(), $filename, '', $required = true);
            }else{
            	debug('Unknow type ');
            	debug($child_object);
            }
        }
	}
    
    protected function get_children($object_id){
    	$rdm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $object_id, ComplexContentObjectItem :: get_table_name());
        $result = $rdm->retrieve_complex_content_object_items($condition);
        return $result;
    }
    
    protected function retrieve_object($ref){
    	$rdm = RepositoryDataManager :: get_instance();
    	$result = $rdm->retrieve_content_object($ref);
    	if($result instanceof ComplexContentObjectItem){
    		$result = $rdm->retrieve_content_object($result->get_ref());
    	}
    	return $result;
    }
    
    protected function create_exporter($object){
    	$result = QtiExport::factory_qti($object, $this->get_temp_directory(), $this->get_manifest(), $this->get_toc());
    	return $result;
    }
	
	
}







?>