<?php

abstract class InternshipOrganizerImport
{
    
	const TEMP_FILE_NAME = 'tmp_name';
	
	const REGION = 'region';
	const CATEGORY = 'category';
	const ORGANISATION = 'organisation';
    
    /**
     * The imported lo's file properties.
     */
    private $internship_organizer_file;
    
    /**
     * The user importing the lo.
     */
    private $user;
    
    /**
     * The type of the imported object.
     */
    private $object_type;

    /**
     * List of import messages.
     * @var array of string
     */
    private $messages = array();
    
    /**
     * List of import warnings;
     * @var array of string
     */
    private $warnings = array();
    
    /**
     * List of import errors.
     * @var array of string
     */
    private $errors = array();
    
    /**
     * Constructor
     * @param string $filename
     */
    public function InternshipOrganizerImport($internship_organizer_file, $user, $object_type)
    {
        $this->internship_organizer_file = $internship_organizer_file;
        $this->user = $user;
        $this->object_type = $object_type;
        $this->messages = array();
        $this->warnings = array();
        $this->errors = array();
    }

    /**
     * Gets the internship organizer file
     * @return array
     */
    function get_internship_organizer_file()
    {
        return $this->internship_organizer_file;
    }

    function get_user()
    {
        return $this->user;
    }

    function get_object_type()
    {
        return $this->object_type;
    }

    function get_messages(){
    	return $this->messages;
    }

    function add_message($message){
    	$this->messages[] = $message;
    }
    
    function add_messages($messages){
    	foreach($messages as $message){
    		$this->add_message($message);
    	}
    }
    
    function clear_messages(){
    	$this->messages = array();
    }

    function get_warnings(){
    	return $this->warnings;
    }
    
    function add_warning($warning){
    	$this->warnings[] = $warning;
    }

    function add_warnings($messages){
    	foreach($messages as $message){
    		$this->add_warning($message);
    	}
    }
    
    function clear_warnings(){
    	$this->warnings = array();
    }

    function get_errors(){
    	return $this->errors;
    }
    
    function add_error($error){
    	$this->errors[] = $error;
    }
    
    function add_errors($messages){
    	foreach($messages as $message){
    		$this->add_error($message);
    	}
    }
    
    function clear_errors(){
    	$this->errors = array();
    }
    
    /**
     * Gets a internship organizer file property
     * @return array
     */
    function get_internship_organizer_file_property($name)
    {
    	return $this->internship_organizer_file[$name];
    }

    /**
     * Gets the supported filetypes for import
     * @return array Array containig all supported filetypes (keys and values
     * are the same)
     */
    public static function get_supported_filetypes()
    {
        $directories = Filesystem :: get_directory_content(dirname(__FILE__), Filesystem :: LIST_DIRECTORIES, false);
        foreach ($directories as $index => $directory)
        {
            $type = basename($directory);
            if ($type != '.svn')
            {
                $types[$type] = $type;
            }
        }
        return $types;
    }

    public static function type_supported($type)
    {
        return in_array($type, self :: get_supported_filetypes());
    }

    /**
     * Factory function to create an instance of an import class
     * @param string $type One of the supported file types returned by the
     * get_supported_filetypes function.
     * @param string $filename The desired filename for the export file
     * (extension will be automatically added depending on the given $type)
     */
    public static function factory($type, $internship_organizer_file, $user, $object_type)
    {
        
    	
    	$file = dirname(__FILE__) . '/' . $type . '/' . $type .'_'. $object_type.'_import.class.php';
        $class = Utilities :: underscores_to_camelcase($type.'_'. $object_type) . 'Import';
        
        if (file_exists($file))
        {
            require_once ($file);
            return new $class($internship_organizer_file, $user, $object_type);
        }
    }

    protected function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    abstract function import_internship_organizer_object();
}
?>