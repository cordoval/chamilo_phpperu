<?php

abstract class BaseExternalRepository 
{
    const GET_NEW_UID_NOT_IMPLEMENTED = 'GET_NEW_UID_NOT_IMPLEMENTED';
    const SESSION_MISSING_FIELDS      = 'SESSION_MISSING_FIELDS';
    
    /*
     * Constants used as properties names to describe an object stored in an external repository 
     */
    const OBJECT_ID                = 'object_id';
    const OBJECT_TITLE             = 'object_title';
    const OBJECT_SYNC_STATE        = 'object_sync_state';
    const OBJECT_OWNER_ID          = 'object_owner_id';
    const OBJECT_CREATION_DATE     = 'object_creation_date';
    const OBJECT_MODIFICATION_DATE = 'object_modification_date';
    const OBJECT_DESCRIPTION       = 'object_description';
    
    const EXTERNAL_OBJECT_KEY      = 'external_object';
    const CHAMILO_OBJECT_KEY       = 'content_object';
    const SYNC_INFO                = 'sync_info';
    
    const SYNC_STATE               = 'sync_state';
    const SYNC_NEVER_SYNCHRONIZED  = 'never_synchronized';
    const SYNC_IDENTICAL           = 'sync_synchronized';
    const SYNC_NEWER_IN_CHAMILO    = 'newer_in_chamilo';
    const SYNC_OLDER_IN_CHAMILO    = 'older_in_chamilo';
    
    protected $errors = array();
    
    /**
     * @var ExternalExport
     */
    private $external_export = null;
    
    /*
     * List of LomMapper objects. Stored for any content_object id  
     */
    private $lom_mappers = null;
     
    /*************************************************************************/
    
	protected function BaseExternalRepository($external_export_id = DataClass :: NO_UID) 
	{
	    if($external_export_id != DataClass :: NO_UID)
		{
		    $this->load_configuration($external_export_id);
		}
	}
	
	
	/*************************************************************************/
	
	/**
	 * Return an instance of a BaseExternalRepository subclass
     * 
     * If a specific class exists for the configured export, this one is instanciated and returned.
     * If it doesn't exist, an instance of the generic class for the repository type is returned.
     * 
	 * @param $export ExternalExport
	 * @return BaseExternalRepository A subclass of BaseExternalRepository
	 */
	public static function get_instance($export)
	{
	    $export_type  = strtolower($export->get_type());
	    $catalog_name = strtolower($export->get_catalog_name());
	    
	    $class_name = null;
	    if(file_exists(Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/custom/' . strtolower($catalog_name)  . '_external_exporter.class.php'))
	    {
	        require_once Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/custom/' . strtolower($catalog_name)  . '_external_exporter.class.php';
	        $class_name = Utilities :: underscores_to_camelcase($catalog_name) . 'ExternalExporter';
	    }
	    else
	    {
	        require_once Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/' . strtolower($export_type)  . '_external_exporter.class.php';
	        $class_name = Utilities :: underscores_to_camelcase($export_type) . 'ExternalExporter';
	    }
	    
	    if(isset($class_name))
	    {
	        return new $class_name($export->get_id());
	    }
	    else
	    {
	        throw new Exception('Export type \'' . $export_type . '\' not implemented');
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Get the export configuration from the datasource and set the class variables.
	 * 
	 * @return void
	 */
	private function load_configuration($external_export_id)
	{
	    if(isset($external_export_id) && strlen($external_export_id) > 0 && $external_export_id != DataClass :: NO_UID)
	    {
    	    $export = new ExternalExport();
    	    $export->set_id($external_export_id);
    	    $typed_export = $export->get_typed_export_object();
    	    
    	    if(isset($typed_export) && is_a($typed_export, 'ExternalExport'))
    	    {
    	        $this->set_external_export($typed_export);
    	    }
    	    else
    	    {
    	        throw new Exception('Unable to load external export configuration');
    	    }
	    }
	    else
	    {
	        throw new Exception('Unable to load external export configuration');
	    }
	}
	
	public function set_external_export($external_export)
	{
	    $this->external_export = $external_export;
	}
	
	
	/**
	 * 
	 * @return ExternalExport
	 */
	public function get_external_export()
	{
	    return $this->external_export;
	}
	
	
	/**
	 * 
	 * @param $content_object ContentObject
	 * @return IeeeLomMapper
	 */
	protected function get_lom_mapper($content_object = null)
	{
	    if(isset($this->lom_mappers))
	    {
	        $this->lom_mappers = array();
	    }
	    
	    if(isset($content_object) && isset($this->lom_mappers[$content_object->get_id()]))
	    {
	       return $this->lom_mappers[$content_object->get_id()];
	    }
	    elseif(isset($content_object))
	    {
	        $lom_mapper = new IeeeLomMapper($content_object);
	        $lom_mapper->get_metadata();
	        $this->lom_mappers[$content_object->get_id()] = $lom_mapper;
	        return $lom_mapper;
	    }
	    else
	    {
	        throw new Exception('Metadata mapper is not set');
	    }
	    
//	    if(isset($this->lom_mapper))
//	    {
//	       return $this->lom_mapper;
//	    }
//	    elseif(isset($content_object))
//	    {
//	        $this->lom_mapper = new IeeeLomMapper($content_object);
//	        $this->lom_mapper->get_metadata();
//	        return $this->lom_mapper;
//	    }
//	    else
//	    {
//	        throw new Exception('Metadata mapper is not set');
//	    }
	}
	
	
	/**
	 * Check if the minimum metadata required for the object to be exported are present 
	 * 
	 * @param $content_object ContentObject
	 * @return boolean Indicates wether the required metadata are present or not.
	 */
	public function check_required_metadata($content_object)
	{
	    return true;
	}
	
	
	/**
	 * Export the learning object to the external repository
	 * 
	 * @param $content_object ContentObject Learning object to export to the external repository
	 * @return boolean Indicates wether the export succeeded
	 */
	abstract public function export($content_object);
	
	/**
	 * Import an object from an external repository and create or update a ContentObject in the Chamilo datasource
	 * 
	 * @param integer $repository_object_id
	 * @param integer $owner_id The user id that will be the owner id of the object after the import
	 * @return boolean Indicates wether the import succeeded
	 */
	abstract public function import($repository_object_id, $owner_id);
	
	/**
	 * Return an array of properties on an object stored in an external repository
	 * 
	 * @param mixed $repository_object_id
	 * @return array
	 */
	abstract function get_repository_object_infos($repository_object_id);
	
	/**
	 * Return the list of objects existing in the repository. This list is specific to the logged in user.
	 * The returned array should contain object properties (such as title, type, ...) and infos about its state compared to Chamilo (such as synchronized, newer in repository, ...)  
	 * 
	 * @return array
	 */
	abstract public function get_objects_list_from_repository();
	
	/**
	 * Prepare the learning object for the export.
	 * Ensure it has an UID valid for the repository
	 * 
	 * @param $content_object ContentObject Learning object to export to the external repository
	 * @return boolean Indicates wether the learning object could be prepared for export
	 */
	protected function prepare_export($content_object)
	{
	    $lom_mapper  = $this->get_lom_mapper($content_object);
	    
	    /*
	     * Get the different identifiers from the metadata 
	     * and checks if a new UID for the external repository has to be assigned to the object
	     */
	    if(!$this->check_repository_uid($content_object))
	    {
	        /*
	         * Get a new UID and assign to the object
	         * Then save it in the datasource.
	         * 
	         * Note: 	saving it before sending the object to the external repository allows
	         * 			to ensure that an object can not be exported without its external UID
	         * 			saved in the datasource
	         */
	        $lom_mapper->add_general_identifier($this->external_export->get_catalog_name(), $this->get_new_uid());
	        $lom_mapper->save_metadata();
	    }
	    
	    return true;

//	    /*
//	     * Get the file to export
//	     */ 
//	    //$size = $content_object->get_size();
//	    
//	    $dlof = new DlofExport($content_object);
//	    $zippath = $dlof->export_content_object();
//	    
//	    debug($zippath);
	}
	
	
	/**
	 * 
	 * @param $content_object ContentObject
	 * @return string
	 */
	protected function get_content_object_metadata_xml($content_object)
	{
	    $lom_mapper  = $this->get_lom_mapper($content_object);
	    
	    /*
	     * Retrieve the LOM-XML metadata that will be used for the export 
	     */
	    $lom_document = new DOMDocument();
	    $lom_document->loadXML($lom_mapper->export_metadata(false, true));
	    
	    /*
	     * Apply an XSL transformation if any XSL filename is configured
	     * Note: the XSL file must be placed in the 'xsl' repository 
	     */
	    $metadata_document = $this->process_metadata_xsl($lom_document, $this->get_external_export()->get_metadata_xsl_filename());
	    //debug($metadata_document);
	    
	    //return $metadata_document->saveXML();
	    return $metadata_document;
	}
	
	
	/**
	 * Check if the learning object has an UID valid for the external repository
	 *  
	 * @return boolean Indicates wether the learning object has an UID valid for the external repository
	 */
	protected function check_repository_uid($content_object)
	{
	    $repository_uid = $this->get_existing_repository_uid($content_object);
	    
	    return isset($repository_uid);
	}
	
	/**
	 * Get the UID corresponding to the external repository (if it exists in the learning objet metadata)
	 *  
	 * @return string The UID corresponding to the external repository
	 */
	public function get_existing_repository_uid($content_object)
	{
	    if(isset($this->external_export))
	    {
    	    $sync_infos = ExternalRepositorySyncInfo :: get_by_content_object_and_repository($content_object->get_id(), $this->external_export->get_id());
    	    
    	    if(isset($sync_infos))
    	    {
    	        return $sync_infos->get_external_object_uid();
    	    }
    	    else
    	    {
    	        return null;
    	    }
	    }
	    else
	    {
	        throw new Exception('External export not defined');
	    }
	    
	    /*******************************************/
	    
//	    if(isset($this->external_export))
//	    {
//    	    /*
//    	     * Basic metadata type is LOM
//    	     */
//    	    $lom_mapper  = $this->get_lom_mapper($content_object);
//    	    $lom_mapper->get_metadata();
//    	    $identifiers = $lom_mapper->get_identifier();
//    	    
//    	    //debug($this->external_export->get_default_properties());
//    	    
//    	    foreach ($identifiers as $identifier)
//    	    {
//    	    	//debug($identifier);
//    	    	
//    	    	if($identifier['catalog'] == $this->external_export->get_catalog_name())
//    	    	{
//    	    	    return $identifier['entry'];
//    	    	}
//    	    }
//    	    
//    	    return null;
//	    }
//	    else
//	    {
//	        throw new Exception('External export not defined');
//	    }
	}
	
	public function get_new_uid()
	{
	    $new_external_uid = $this->get_repository_new_uid();
	    
	    if($new_external_uid == self :: GET_NEW_UID_NOT_IMPLEMENTED)
	    {
	        return $this->get_local_new_uid();
	    }
	    elseif($new_external_uid !== false)
	    {
	        return $new_external_uid;
	    }
	    else
	    {
	        throw new Exception('An error occured while retrieving a new uid for the object to export');
	    }
	}
	
	
	/**
	 * Return a new uid generated by the external repository, or a value indicating it is not configured, or a value indicating the retrieval failed
	 * 
	 * @return mixed Return a string that is the new uid or a constant if the external repository is not able to generate a new UID. 
	 * 		   May also return false if the new uid retrieval is configured, but fails.  
	 */
	public function get_repository_new_uid()
	{
	    return self :: GET_NEW_UID_NOT_IMPLEMENTED;
	}
	
	
	/**
	 * - Store the last modification of the object at the time it is exported
	 * - If available, get the last modification date from the repository and store it
	 * 
	 * @param ContentObject $content_object
	 * @param $repository_object_id string Fedora object uid
	 * @return boolean
	 */
	abstract function store_last_repository_update_datetime($content_object, $repository_object_id);
	
	
	/**
	 * Create a new uid. This function should be called when the external repository 
	 * is not able to generate a new UID to set on the learning object
	 * 
	 * @return string New UID
	 */
	public function get_local_new_uid()
	{
	    return uniqid(str_replace('www', '', $_SERVER['HTTP_HOST']) . ':chamilo:');
	}
	
	
	/**
	 * 
	 * @param $original_document DOMDocument The document to process
	 * @param $xsl_filename The XSL filename
	 * @return DOMDocument
	 */
	protected function process_metadata_xsl($original_document, $xsl_filename)
	{
	    if(isset($xsl_filename) && strlen($xsl_filename) > 0)
	    {
    	    if(file_exists(dirname(__FILE__) . '/xsl/' . $xsl_filename))
    	    {
        	    $xsl = new DOMDocument;
        		$xsl->load(dirname(__FILE__) . '/xsl/' . $xsl_filename);
        		
        		$proc = new XSLTProcessor;
        		$proc->importStylesheet($xsl);
        	    
        		return $proc->transformToDoc($original_document);
    	    }
    	    else
    	    {
    	        throw new Exception('XSL file \'' . $xsl_filename . '\' not found');
    	    }
	    }
	    else
	    {
	        return $original_document;
	    }
	}
	
	
	/**
	 * Return a unique id that can be used outside of Chamilo. 
	 * If the user doesn't have any external_uid set in the datasource, his email is returned.   
	 * 
	 * @return string 
	 */
	protected function get_user_external_identifier()
	{
	    $user_id = Session :: retrieve('_uid');
	    $udm     = UserDataManager :: get_instance();
		$user    = $udm->retrieve_user($user_id);
	    
		$external_uid = $user->get_external_uid();
		
		if(isset($external_uid))
		{
		    return $external_uid;
		}
		else
		{
		    return $user->get_email(); 
		}
	}
	
	
}
?>