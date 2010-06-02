<?php
/*
require_once('main.php');

class qformat_imsqti extends qformat_default {
	
	const IMS_MANIFEST_NAME = 'imsmanifest.xml';

	private $base_temp = '';
	private $resources = array();
	
	//MOODLE qformat_default interface

  	function provide_import(){
    	global $CFG;
        return $CFG->version >= 2007101509; // i.e. moodle 1.9 or later
  	}
  	
  	function provide_export(){
    	global $CFG;
        return $CFG->version >= 2007101509; // i.e. moodle 1.9 or later
  	}

    function export_file_extension() {
        return '.zip';
    }
    
 	function importprocess() {
 		$log = new LogOnline();
 		$importer = new QtiImport($log);
 		return $importer->import($this->filename, $this->realfilename, $this->course, $this->category, $this->stoponerror);
 		
 		
 		try{
 			return $this->execute_import();
 		}catch(Exception $e){
 			DebugUtil::print_exception($e);
 			return false;
 		}
 	}
 	
	function exportprocess(){
	    if(!$export_dir = make_upload_directory($this->question_get_export_dir())) {
        	$this->notify_lang('cannotcreatepath');
        	return false;
        }
        
 		$log = new LogOnline();
		$export = new QtiExport($log);
		$questions = $export->get_questions($this->category);
        $this->notify_lang('exporting', count($questions));
		$export->add($questions);
		if($export->get_question_count()==0){
            $this->notify_lang('noquestions');
        }else{
        	$export->save($this->get_export_file_path());
        }
        return true;
    }
 	
 	//END MOODLE qformat_default interface
 	
 	protected function execute_import(){
       	$this->notify_lang('parsingquestions');
       	
       	if(!$this->is_uploaded_file_xml()){
       		$questions = array();
       		$temp = $this->extract_archive();
       		$questions = $this->read_directory($temp);
       	}else if (! $questions = $this->read_questions($this->filename)){
       		$this->notify_lang('cannotread');
            return false;
        }
        
        $this->notify_lang('importingquestions', $this->count_questions($questions));

        // check for errors before we continue
        if ($this->stoponerror && $this->importerrors>0) {
            $this->notify_lang('importparseerror');
            return false;
        }
        
/*
        // get list of valid answer grades
        $grades = get_grade_options();
        $gradeoptionsfull = $grades->gradeoptionsfull;

        // check answer grades are valid
        // (now need to do this here because of 'stop on error': MDL-10689)
        $gradeerrors = 0;
        $goodquestions = array();
        foreach ($questions as $question) {
            if (!empty($question->fraction) and (is_array($question->fraction))) {
                $fractions = $question->fraction;
                $answersvalid = true; // in case they are!
                foreach ($fractions as $key => $fraction) {
                    $newfraction = match_grade_options($gradeoptionsfull, $fraction, $this->matchgrades);
                    if ($newfraction===false) {
                        $answersvalid = false;
                    }
                    else {
                        $fractions[$key] = $newfraction;
                    }
                }
                if (!$answersvalid) {
                    echo $OUTPUT->notification(get_string('matcherror', 'quiz'));
                    ++$gradeerrors;
                    continue;
                }
                else {
                    $question->fraction = $fractions;
                }
            }
            $goodquestions[] = $question;
        }
        $questions = $goodquestions;
        // check for errors before we continue
        if ($this->stoponerror and ($gradeerrors>0)) {
            return false;
        }

*/
   /*     $count = 0;
        foreach($questions as $question){
            $this->reset_time_limit();

            if($question->qtype=='category'){
            	if($category = $this->save_category($question)) {
            		$this->category = $category;
            	}
            }else{
	            echo '<hr/><p><b>'.++$count.'</b>. '.$this->format_question_text($question).'</p>';
	            $result = $this->save_question($question);
		        if (!empty($result->error)) {
		        	$this->notify($result->error);
		        	return false;
		        }
		
		        if (!empty($result->notice)) {
		            $this->notify($result->notice);
		        }
	        }
        }
        $this->import_resources();
        $this->cleanup();
       	$this->notify_lang('done', '', 'quiz_overview');
        return true;
 	}
 	
 	protected function read_directory($directory){
 		$result = array();
 		$directory = rtrim($directory, '/') . '/';
 		$entries = scandir($directory);
       	foreach($entries as $entry){
       		$path = $directory . $entry;
       		if(is_file($path) && $this->is_question_file($path)){
       			if($file_questions = $this->read_questions($path)){
       				echo $entry .'<br/>';
       				$result = array_merge($result, $file_questions);
       			}
       		}else if(is_dir($path) && $entry != '.' && $entry != '..' ){
       			$directory_questions = $this->read_directory($path);
       			$result = array_merge($result, $directory_questions);
       		}
       	}	
       	return $result;
 	}
 	
 	protected function is_question_file($path){
 		$name = basename($path);
 		$ext = pathinfo($path, PATHINFO_EXTENSION);
 		if(empty($ext) || $ext != 'xml' || self::IMS_MANIFEST_NAME == $name){
 			return false;
 		}
 		$reader = new ImsQtiReader();
 		$reader->load($path);
 		return count($reader->query('/def:assessmentItem'))>0;
 	}
 	
 	protected function extract_archive(){
 		$temp = $this->create_temp_directory();
    	$zipper = new zip_packer();
    	if($zipper->extract_to_pathname($this->filename, $temp)){
    		return $temp;
    	}else{
    		return false;
    	}	
 	}
 	/*
 	protected function create_temp_directory(){
    	global $CFG;
    	$result = $this->base_temp = $CFG->dataroot.'/temp/'.time().'/';
    	fulldelete($result);
    	$this->ensure_directory($result);
    	return $result;
 	}
 	*/
 /*	protected function is_uploaded_file_xml(){
 		$name = $this->realfilename;
 		$ext = pathinfo($name, PATHINFO_EXTENSION);
 		return $ext == 'xml';
 	}
 	
  	protected function read_questions($filename){
  		$result = array();
  		$reader = new ImsQtiReader($filename, false);
  		$items = $reader->query('/def:assessmentItem');
  		foreach($items as $item){
			if($builder = $this->create_builder($item)){
  				$question = $builder->build($item);
  				
  				if(empty($question)){
  					$this->notify_lang('importerror'); 
  				}else{
  					$result[] = $question;
  					
  					$this->resources = array_merge($this->resources, $builder->get_resources());
  				}
  			}else{
  				$this->notify_lang('unknownquestiontype', $item->title .' (' . basename($filename) .')'); 
  			}
  		}
  		return $result;
  	}
  	
  	protected function save_question($question){
  		global $USER, $DB;
  		
  		if(!isset($question->course)){
  			$question->course = $this->course->id;
  		}
  		
  		$question->category = $this->category->id;
        $question->stamp = make_unique_id_code();  // Set the unique code (not to be changed)

        $question->createdby = $USER->id;
        $question->timecreated = time();

        $question->id = $DB->insert_record("question", $question);
        $this->questionids[] = $question->id;

        //save type-specific options
        global $QTYPES;
        $result = $QTYPES[$question->qtype]->save_question_options($question);

        // Give the question a unique version stamp determined by question_hash()
        $DB->set_field('question', 'version', question_hash($question), array('id'=>$question->id));

        return $result;
  	}
  	
  	protected function save_category($question){
        if ($question->qtype=='category' && $this->catfromfil) {
			// find/create category object
			$catpath = $question->category;
            return $this->create_category_path($catpath, '/');
        }else{
        	return null;
        }
  	}
  	  	
  	protected function cleanup(){
        fulldelete($this->filename);
        fulldelete($this->get_base_temp());
  	}
  	
  	protected function get_base_url(){
  		$file = $this->get_base_file();
  		$result = "/moodle/file.php/{$this->course->id}/$file/";
  		return $result;
  	}
  	
  	protected function get_base_file(){
  		$result = explode('.', basename($this->realfilename));
  		return $result[0];
  	}
  	
  	protected function get_base_temp(){
  		return $this->base_temp;
  	}

  	protected function get_base_target(){
  		$file = $this->get_base_file();
  		global $CFG;
  		return "{$CFG->dataroot}/{$this->course->id}/$file/";
  	}

  	protected function get_export_file_path(){
  		global $CFG;
        $result = $CFG->dataroot.'/'.$this->question_get_export_dir().'/'.$this->filename.'.zip';
        return $result;
  	}
  	
  	protected function create_builder($item){
  		return QuestionBuilder::factory($item, $this->get_base_temp(), $this->get_base_url());
  	}
  	/*
  	protected function ensure_directory($path){
  		$path = rtrim($path, '/');
  		global $CFG;
  		$is_dir = strpos(basename($path), '.') === false;
  		$dir = $is_dir ? $path : dirname($path);
  		if(!file_exists($dir)){
  			$result = mkdir($dir, '0777', true);
  			return $result;
  		}else{
  			return true;
  		}
  	}*/
  	
/*  	protected function import_resources(){
  		$files = $this->resources;
  		foreach($files as $url => $path){
  			$this->import_resource($path);
  		}
  	}
  	
  	protected function import_resource($path){
  		try{
	    	$context = get_context_instance(CONTEXT_COURSE, $this->course->id); 
	  		$contextid = $context->id;
	        $from_path = $path;
	        $filepath = dirname($path);
	  		$filepath = str_replace($this->get_base_temp(), '', $filepath);
	  		$filepath = '/'. $this->get_base_file() .'/'. trim($filepath, '/') .'/' ;
	  		$filearea = 'course_content';
	  		$filename = basename($path);  
	  		$itemid = 0;
	    
	    	$fs = get_file_storage(); 
	    	if($fs->file_exists($contextid, $filearea, $itemid, $filepath, $filename)){
	    		$fs->delete_area_files($contextid, $filearea, $itemid);
	    		$fs->cron();	
	    	}
	    	
	    	$file_record = array('contextid'=>$contextid, 'filearea'=>$filearea, 'itemid'=>$itemid, 
	    						'filepath'=>$filepath, 'filename'=>$filename,         
	    						'timecreated'=>time(), 'timemodified'=>time()); 
	    	$fs->create_file_from_pathname($file_record, $from_path);
  		}catch(Exception $e){
  			debug($e->getMessage());
  			$this->notify_lang('cannotimportfile', basename($path));
  		}
  	}

  	protected function print_question($question, $count){
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $format = empty($question->questiontextformat) ? FORMAT_MOODLE : $question->questiontextformat;
        $question_text = format_text($question->questiontext, $format, $formatoptions);
        $this->write("<hr/><p><b>$count</b>. $question_text</p>");
  	}
  	
  	protected function notify($message){
  		global $OUTPUT;
  		if(is_array($message)){
  			foreach($message as $m){
       			echo $OUTPUT->notification($message);
  			}
  		}else{
       		echo $OUTPUT->notification($message);
  		}
  	}
  	
  	protected function notify_lang($message, $a ='', $module = ''){
  		if(empty($module)){
  			$module = 'quiz';
  		}
  		if(empty($a)){
  			$text = get_string($message, $module);
  		}else{
  			$text = get_string($message, $module, $a);
  		}	
  		$text = empty($a) ? $text : $text . ': ' . $a; 
  		$this->notify($text);
  	}

 	protected function reset_time_limit(){
 		$max_time =get_cfg_var('max_execution_time');
 		set_time_limit($max_time); 
 	}
}
*/

