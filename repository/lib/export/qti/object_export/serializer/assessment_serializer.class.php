<?php

/**
 * 
 * @copyright (c) 2010 University of Geneva 
 * @author laurent.opprecht@unige.ch
 *
 */
class AssessmentSerializer extends SerializerBase{
	
	static function factory($object, $target_root, $directory, $manifest, $toc){
		if($object instanceof Assessment){
			return new self($target_root, $directory, $manifest, $toc);
		}else{
			return null;
		}
	}
	
	public function serialize(Assessment $assessment){
        $writer = new ImsQtiWriter();
        $assessment_id = self::get_identifier($assessment);
        $test = $writer->add_assessmentTest($assessment_id, $assessment->get_title(), Qti::get_tool_name(), Qti::get_tool_version());
        $part = $test->add_testPart(null, Qti::NAVIGATION_MODE_LINEAR, Qti::SUBMISSION_MODE_INDIVIDUAL);
        $part->add_itemSessionControl(0, true, true, true, true, true, false);
        $section = $part->add_assessmentSection(null, $assessment->get_title(), false);
        $instruction = $this->translate_text($assessment->get_description());
        
        $section->add_rubricBlock(Qti::VIEW_ALL)->add_flow($instruction);
        
        $this->add_children($section, $assessment->get_id());
        return $writer->saveXML();
	}
	
	protected function add_children(ImsQtiWriter $writer, $parent_id){
        $children = $this->retrieve_children($parent_id);
        while ($child = $children->next_result()){
            $child_object = $this->retrieve_object($child->get_ref());
			if($exporter = $this->create_exporter($child_object)){
	            $path = $exporter->export_content_object();
	            $filename = basename($path);
	            $ref = $writer->add_assessmentItemRef('ID_'.$child->get_id(), $filename, '', $required = true);
	            $ref->add_weight(null, $child->get_weight());
            }else{
            	debug('Unknow type ');
            	debug($child_object);
            }
        }
	}
    
    protected function retrieve_children($object_id){
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