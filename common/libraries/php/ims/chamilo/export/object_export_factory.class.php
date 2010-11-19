<?php
namespace common\libraries;

class ObjectExportFactory {
	
	private $failover = null;
    private $directory = '';
    private $function = 'factory';
    private $file_trailer = '.class.php';
    
    public function __construct($failover, $directory = '', $function = '', $file_trailer = ''){
    	$this->failover = $failover;
    	$this->directory = empty($directory) ? dirname(__FILE__) . '/builder/' : $directory;
    	$this->function = empty($function) ? 'factory' : $function;
    	$this->file_trailer = empty($file_trailer) ? '.class.php' : $file_trailer;  
    }
    
    public function get_directory(){
    	return $this->directory;
    }
    
    public function get_function(){
    	return $this->function;
    }
    
    public function get_file_trailer(){
    	return $this->file_trailer;
    }
    
    public function get_failover(){
    	return $failover;
    }

	public function create(ObjectImportSettings $settings){
    	if($import = $this->create_from_directory($settings)){
    		return new BufferedObjectExport($settings, $import);
    	}
    	if(!empty($this->failover)){
    		$args = func_get_args();
        	$f = array($this->failover, $this->function);
        	if($import = call_user_func_array($f, $args)){
        		return new BufferedObjectExport($settings, $import);
        	}
    	}
    		
    	$file_name = basename($settings->get_path());
    	$settings->get_log()->error(Translation :: get('ObjectNotExported', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES). ': ' .$file_name);
    	return EmptyObjectImport::get_instance();
	}
	
	protected function create_from_directory(ObjectImportSettings $settings){
		$file_trailer = $this->file_trailer;
		$directory = $this->directory;
		$function = $this->function;
		$files = scandir($directory);
		foreach($files as $file){
			if(StringUtilities::end_with($file, $file_trailer, false)){
				require_once $directory . $file;
				$type = str_replace($file_trailer, '', $file);
        		$class = Utilities::underscores_to_camelcase($type);
        		$args = func_get_args();
        		$f = array($class, $function);
        		if($result = call_user_func_array($f, $args)){
        			return $result;
        		}
			}
		}
		return null;
	}
    

}












?>