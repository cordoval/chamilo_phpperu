<?php
require_once Path :: get_repository_path() . '/lib/export/external_export/rest_external_exporter.class.php';
require_once Path :: get_plugin_path() . '/webservices/rest/client/rest_client.class.php';

/**
 * This class is a basic implementation of learning object export to a Fedora repository (http://www.fedora-commons.org)
 * The export configuration are stored in the 'repository_external_export' and 'repository_external_export_fedora' tables of the datasource.
 * 
 * 
 * BASIC FEATURES
 * ==============
 * 
 * This export implements the following features (see the 'export' function):
 * 
 * - Check if the Learning Object has already been exported to the Fedora repository by checking if it already has an identifier for this external repository in its metadata
 * 		- if not, retrieve a new uid from the repository (through the REST API) and store it in the LO metadata
 * 		
 * 		Note: this Fedora uid will allow to differentiate NEW objects from objects to UPDATE in Fedora  
 * 
 * - Check if minimum required metadata are available.
 * 		- if some required metadata are missing, the metadata edition form is shown.
 * 
 * 		Note: 	By default, this check always returns true. If you need to implement you own check, create your own Fedora export class inheriting from 'FedoraExternalExporter'
 * 				and override the 'check_required_metadata' function
 * 
 * - Create a new object in the Fedora repository if it doesn't exist yet
 * - Create a datastream called 'LOM' containing the LOM-XML of the learning object
 * - Create a datastream called 'OBJECT' with the learning object content
 * 
 * 
 * ADDING SPECIFIC FEATURES
 * ========================
 * 
 * Exporter
 * --------
 * If you need to implement specific business logic during the export to your Fedora repository, you can create your own export class inheriting from 'FedoraExternalExporter' 
 * and override the functions you need to customize.
 * 
 * In order to be called automatically, you own class name should start with the camelized version of the 'catalog_name' field value of the repository_external_export table in the datasource.
 *  
 * For example, if the 'catalog_name' value is 'fedora_test' and the export 'type' field is 'fedora', the export logic will try to find a class called 'FedoraTestExternalExporter'
 * in /chamilo/repository/lib/export/external_export/fedora/custom/fedora_test_external_exporter.class.php. 
 * If such a class exists, it is used as exporter for the export.
 * If such a class doesn't exist, the basic 'FedoraExternalExporter' class is used for the export 
 * 
 * Form
 * ----
 * If you need to implement a specific form before running the export, you can create your own export form class inheriting from 'ExternalExportExportForm' 
 * and override the functions you need to customize.
 * 
 * Similarly to the Exporter class, the form class name should start with the camelized version of the 'catalog_name' field value of the repository_external_export table in the datasource.
 * 
 * For example, if the 'catalog_name' value is 'fedora_test' and the export 'type' field is 'fedora', the export logic will try to find a class called 'FedoraTestExternalExportForm'
 * in /chamilo/repository/lib/export/external_export/fedora/custom/fedora_test_external_export_form.class.php.
 * If such a class exists, it is used as form for the export.
 * If such a class doesn't exist, the basic 'ExternalExportExportForm' class is used for the export 
 * 
 * 
 * CONFIGURATION
 * =============
 * 
 * For a complete list of configurable properties, see the 'ExternalExportFedora' class properties documentation
 * 
 * 
 * AUTHENTIFICATION
 * ================
 * 
 * Some of the REST requests sent by the exporter need to provide credentials to Fedora. The login + password are retrieved from the 'repository_external_export_fedora' table.
 * 
 * Certificate based client authentification
 * -----------------------------------------
 * It is possible to specify a client certificate to send with the REST requests. The client certificate and the certificate key can be specified as path(es) to the file(s).
 * These pathes are relative to the '/chamilo/repository/lib/export/external_export/ssl' folder.
 * 
 * Note: 
 * 			The content of these files (at least the one containing the private key) is sensitive and must be protected (e.g. through .htaccess file) to be kept private
   			The settings regarding certificates will work only if the 'libcurl' extension is installed
 * 
 * Target authentification
 * -----------------------
 * If your Fedora server uses an SSL certificate signed by your own certificate CA, you can use this CA public certificate with your REST requests to authenticate the target server.
 * 
 * Note:	
 * 			The settings regarding certificates will work only if the 'libcurl' extension is installed
 * 
 * 
 * EXAMPLE
 * =======
 * 
 * These two SQL queries will store an example of export to a Fedora repository working with the test custom classes provided 
 * 
 * INSERT INTO `repository_external_export` (`id`, `title`, `description`, `type`, `catalog_name`, `metadata_xsl_filename`, `typed_external_export_id`, `enabled`, `created`) 
 * VALUES
 * (10, 'Fedora export test', 'An example of export to a Fedora repository. The Fedora main URL is fake and should be customized to suit your needs', 'fedora', 'fedora_test', NULL, 10, 1, NOW());
 * 
 * 
 * INSERT INTO `repository_external_export_fedora` (`id`, `login`, `password`, `base_url`, `get_uid_rest_path`, `find_object_rest_path`, `ingest_rest_path`, `add_datastream_rest_path`, `client_certificate_file`, `client_certificate_key_file`, `client_certificate_key_password`, `target_ca_file`, `created`) 
 * VALUES
 * (10, 'fedoraAdmin', 'fedoraAdmin', 'https://yourserver.com/fedora', 'objects/nextPID?namespace=fedoratest&format=xml', 'objects?pid=true&query=pid%3D{pid}&resultFormat=xml', 'objects/{pid}?label={pid}', 'objects/{pid}/datastreams/{dsID}?controlGroup={controlGroup}&dsLabel={dsLabel}&mimeType={mimeType}', NULL, NULL, NULL, NULL, NOW());
 */
class FedoraExternalExporter extends RestExternalExporter
{
    const DATASTREAM_DC_ID            = 'DC';
    const DATASTREAM_DC_LABEL         = 'Dublin%20Core%20Record%20for%20this%20object';
    
    const DATASTREAM_LOM_ID           = 'LOM';
    const DATASTREAM_LOM_LABEL        = 'Learning%20Object%20Metadata%20XML';
    
    const DATASTREAM_LO_CONTENT_ID    = 'OBJECT';
    const DATASTREAM_LO_CONTENT_LABEL = 'OBJECT';
    
    private $base_url                = null;
    private $get_uid_rest_path       = null;
    private $post_rest_path          = null;
    
    /*************************************************************************/
    
	protected function FedoraExternalExporter($fedora_repository_id = DataClass :: NO_UID) 
	{
		parent :: RestExternalExporter($fedora_repository_id);
	}
	
	/*************************************************************************/
	/*** EXPORT functions ****************************************************/
	/*************************************************************************/
	
	/**
	 * (non-PHPdoc)
	 * @see chamilo/common/external_export/BaseExternalExporter#export($content_object)
	 */
	public function export($content_object)
	{
	    if($this->check_content_object_is_exportable($content_object))
	    {
	        if($this->check_required_metadata($content_object))
	        {
	            /*
	             * Ensure the object has a external uid specific to the target Fedora repository
	             */
        	    $this->prepare_export($content_object);
        	    
        	    /*
        	     * Check if the object already exists in Fedora
        	     * - if not, create it
        	     */
        	    if($this->check_object_exists($content_object))
        	    {
    	            /**************
    	             * UPDATE the dc datastream in Fedora
    	             */
    	            if($this->save_dublin_core_datastream($content_object))
    	            {
    	                /*
        	             * UPDATE the lom datastream in Fedora
        	             */
        	            if(!$this->save_lom_datastream($content_object))
        	            {
            	            throw new Exception('The object LOM metadata could not be updated in Fedora');
        	            }
    	            }
    	            else
    	            {
    	                throw new Exception('The object Dublin Core could not be updated in Fedora');
    	            }
        	    }
        	    else
        	    {
        	        /**************
        	         * CREATE the object in Fedora. The ingestion is based on a a FOXML document
        	         */
        	        
        	        $foxml_doc = $this->get_foxml_document($content_object);
        	        
        	        if(!$this->ingest_foxml_object($foxml_doc))
        	        {
        	            throw new Exception('The object could not be created in Fedora');
        	        }
        	    }
        	    
        	    /**************
	             * CREATE or UPDATE the learning object datastream in Fedora
	             */
	            if($this->save_content_object_datastream($content_object))
	            {
	                return true;
	            }
	            else
	            {
	                throw new Exception('The object content datastream could not be saved in Fedora');
	            }
	        }
	        else
	        {	            
	            Redirect :: url(array(Application :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW, RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => $this->get_external_export()->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
	        }
	    }
	    else
	    {
	        throw new Exception('The object type \'' . $content_object->get_type() . '\' can not be exported');
	    }
	}
	
	
	/**
	 * Check if an object named with the object's repository uid already exists in Fedora.
	 * If not, the object is created in Fedora  
	 * 
	 * @param $content_object ContentObject
	 * @return boolean
	 */
	protected function check_object_exists($content_object)
	{
	    $object_id = $this->get_existing_repository_uid($content_object);
	    //debug($object_id);
	    
	    /*
	     * Search the object
	     */
	    $search_path = $this->get_full_find_object_rest_path();
	    $search_path = str_replace('{pid}', $object_id, $search_path);
	    $response_document = $this->get_rest_xml_response($search_path, 'get');
	    if(isset($response_document))
	    {
	        DebugUtilities :: show($response_document);
	        
	        /*
	         * Check in the XML if the object exists
	         */
	        $xpath = new DOMXPath($response_document);
	        $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
	        
    	    $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields/fedora:pid');
    	    
    	    DebugUtilities :: show($node_list);
    	    
    	    if($node_list->length > 0 && $node_list->item(0)->nodeValue == $object_id)
    	    {
    	        return true;
    	    }
	        else
	        {
        	    /*
        	     * Create the object
        	     */
	            //return $this->create_object_in_fedora($object_id, $ingest_path);
	            return false;
	        }
	    }
	    else
	    {
	        throw new Exception('Unable to check if the object already exists in Fedora');
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Return a FOXML document that can be used to ingest a new object in Fedora
	 * 
	 * @param $content_object
	 * @return DOMDocument A FOXML1-1 DOM document representing the ContentObject
	 */
	protected function get_foxml_document($content_object)
	{
	    $foxml_template_path = Path :: get_repository_path() . 'lib/export/external_export/fedora/foxml-1-1_template.xml';
	    
	    $foxml_doc = new DomDocument();
	    
	    set_error_handler(array($this, 'handle_xml_error'));
	    
	    $foxml_doc->load($foxml_template_path);
	    
	    /*
	     * Set basic infos such as object PID and ownerId
	     */
	    $this->set_basic_foxml_data($content_object, $foxml_doc);
	    
	    /*
	     * Build DC datastream
	     */
	    $this->set_dublin_core_xml_data($content_object, $foxml_doc);
	    
	    /*
	     * Build LOM datastream
	     */
	    $this->set_lom_xml_data($content_object, $foxml_doc);
	    
	    restore_error_handler();
	    
	    return $foxml_doc;
	}
	
	
	/**
	 * Set the basic FOXML metadata in the document, such as object PID and ownerId
	 * 
	 * @param $content_object ContentObject
	 * @param $foxml_doc DomDocument A FOXML document to complete
	 * @return void
	 */
	protected function set_basic_foxml_data($content_object, $foxml_doc)
	{
	    //debug($foxml_doc);
	    
	    $xpath = new DOMXPath($foxml_doc);
	    
	    $root_node  = $xpath->query('/foxml:digitalObject')->item(0);
		$root_node->setAttribute('PID', $this->get_existing_repository_uid($content_object));
	    
		$label_node  = $xpath->query('/foxml:digitalObject/foxml:objectProperties/foxml:property[@NAME="info:fedora/fedora-system:def/model#label"]')->item(0);
		$label_node->setAttribute('VALUE', $content_object->get_title());
		
		$owner_node  = $xpath->query('/foxml:digitalObject/foxml:objectProperties/foxml:property[@NAME="info:fedora/fedora-system:def/model#ownerId"]')->item(0);
		$owner_node->setAttribute('VALUE', $this->get_user_external_identifier());
		
		//debug($foxml_doc);
	}
	
	
	/**
	 * Set the Dublin Core metadata in the document
	 * 
	 * @param $content_object ContentObject
	 * @param $foxml_doc DomDocument A FOXML document to complete
	 * @return void
	 */
	protected function set_dublin_core_xml_data($content_object, $foxml_doc)
	{
	    //debug($foxml_doc);
	    
	    $xpath            = new DOMXPath($foxml_doc);
	    $xpath->registerNamespace("dc","http://purl.org/dc/elements/1.1/");
	    $xpath->registerNamespace("oai_dc","http://www.openarchives.org/OAI/2.0/oai_dc/");
	    
	    $xml_content_node = $xpath->query('/foxml:digitalObject/foxml:datastream[@ID="DC"]/foxml:datastreamVersion/foxml:xmlContent')->item(0);
	    $dc_node          = $xpath->query('/foxml:digitalObject/foxml:datastream[@ID="DC"]/foxml:datastreamVersion/foxml:xmlContent/oai_dc:dc')->item(0);
	    $dc_xml           = $this->get_dublin_core_document($content_object);
	    
	    $dc_doc_node = $foxml_doc->importNode($dc_xml->documentElement, true);
	    $xml_content_node->replaceChild($dc_doc_node, $dc_node);
	}
	
	
	/**
	 * Set the Learning Object Metadata in the document
	 * 
	 * @param $content_object ContentObject
	 * @param $foxml_doc DomDocument A FOXML document to complete
	 * @return void
	 */
	protected function set_lom_xml_data($content_object, $foxml_doc)
	{
	    //debug($foxml_doc);
	    
	    $xpath            = new DOMXPath($foxml_doc);
	    $xml_content_node = $xpath->query('/foxml:digitalObject/foxml:datastream[@ID="LOM"]/foxml:datastreamVersion/foxml:xmlContent')->item(0);
	    $lom_node         = $xpath->query('/foxml:digitalObject/foxml:datastream[@ID="LOM"]/foxml:datastreamVersion/foxml:xmlContent/lom')->item(0);
	    $lom_xml          = $this->get_content_object_metadata_xml($content_object);
	    
	    $lom_doc_node = $foxml_doc->importNode($lom_xml->documentElement, true);
	    $xml_content_node->replaceChild($lom_doc_node, $lom_node);
	    
	    //debug($foxml_doc);
	}
	
	
	/**
	 * Ingest a new object in Fedora by using its FOXML representation
	 * 
	 * @param $foxml_doc A FOXML document to ingest
	 * @return boolean Indicates wether the ingestion of the new object succeeded
	 */
	protected function ingest_foxml_object($foxml_doc)
	{
	    $ingest_path = $this->get_full_ingest_rest_path();
	    
	    /*
	     * Get the object PID from the FOXML document
	     */
	    $xpath = new DOMXPath($foxml_doc);
	    $root_node = $xpath->query('/foxml:digitalObject')->item(0);
	    $object_id = $root_node->getAttribute('PID');
	    
	    $ingest_path = str_replace('{pid}', $object_id, $ingest_path);
	    
	    $response_document = $this->get_rest_xml_response($ingest_path, 'post', $foxml_doc->saveXML(), 'text/xml');
	    
	    if(isset($response_document))
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Save the DC datastream in Fedora 
	 * 
	 * @param $content_object
	 * @return boolean Indicates wether the DC datastream could be saved in Fedora
	 */
	protected function save_dublin_core_datastream($content_object)
	{
	    $dc_doc    = $this->get_dublin_core_document($content_object);
	    $object_id = $this->get_existing_repository_uid($content_object);
	    
	    $replacement_strings                   = array();
	    $replacement_strings['{pid}']          = $object_id;
	    $replacement_strings['{dsID}']         = self :: DATASTREAM_DC_ID;
	    $replacement_strings['{dsLabel}']      = self :: DATASTREAM_DC_LABEL;
	    $replacement_strings['{controlGroup}'] = 'X';
	    $replacement_strings['{mimeType}']     = 'text/xml';
	    
	    return $this->save_datastream($replacement_strings, 'post', $dc_doc->saveXML());
	}
	
	
	/**
	 * Get a Dublin Core XML representation of the ContentObject 
	 * 
	 * @param $content_object ContentObject
	 * @return DomDocument
	 */
	protected function get_dublin_core_document($content_object)
	{
	    $dc_str = '<oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/">
					<dc:title></dc:title>
					<dc:creator></dc:creator>
					<dc:subject></dc:subject>
					<dc:description></dc:description>
					<dc:publisher></dc:publisher>
					<dc:identifier></dc:identifier>
				</oai_dc:dc>';
	    
	    $dc_doc = new DOMDocument();
	    $dc_doc->loadXML($dc_str);
	    
	    $xpath = new DOMXPath($dc_doc);
	    $xpath->registerNamespace("dc","http://purl.org/dc/elements/1.1/");
	    $xpath->registerNamespace("oai_dc","http://www.openarchives.org/OAI/2.0/oai_dc/");
	    
	    $title_node       = $xpath->query('/oai_dc:dc/dc:title')->item(0);
	    $creator_node     = $xpath->query('/oai_dc:dc/dc:creator')->item(0);
	    $subject_node     = $xpath->query('/oai_dc:dc/dc:subject')->item(0);
	    $description_node = $xpath->query('/oai_dc:dc/dc:description')->item(0);
	    $publisher_node   = $xpath->query('/oai_dc:dc/dc:publisher')->item(0);
	    $identifier_node  = $xpath->query('/oai_dc:dc/dc:identifier')->item(0);
	    
		$lom_mapper = $this->get_lom_mapper($content_object);
		$descriptions = $lom_mapper->get_descriptions();
		$titles = $lom_mapper->get_titles();
		
	    $title_node->nodeValue       = (isset($titles) && count($titles) > 0) ? $titles->get_string(0) : $content_object->get_title();
	    $creator_node->nodeValue     = $this->get_user_external_identifier();
	    $subject_node->nodeValue     = (isset($titles) && count($titles) > 0) ? $titles->get_string(0) : $content_object->get_title();
	    $description_node->nodeValue = (isset($descriptions) && count($descriptions) > 0) ? $descriptions[0]->get_string(0) : $content_object->get_description();
	    $publisher_node->nodeValue   = $this->get_user_external_identifier();
	    $identifier_node->nodeValue  = $this->get_existing_repository_uid($content_object);
	    
	    return $dc_doc;
	}
	
	
	/**
	 * Save the LOM datastream in Fedora with a LOM-XML representation of the object 
	 * 
	 * @param $content_object ContentObject
	 * @return boolean Indicates wether the LOM datastream could be saved in Fedora
	 */
	protected function save_lom_datastream($content_object)
	{
	    $lom_xml     = $this->get_content_object_metadata_xml($content_object);
	    $object_id   = $this->get_existing_repository_uid($content_object);
	   
	    $replacement_strings                   = array();
	    $replacement_strings['{pid}']          = $object_id;
	    $replacement_strings['{dsID}']         = self :: DATASTREAM_LOM_ID;
	    $replacement_strings['{dsLabel}']      = self :: DATASTREAM_LOM_LABEL;
	    $replacement_strings['{controlGroup}'] = 'X';
	    $replacement_strings['{mimeType}']     = 'text/xml';
	    
	    return $this->save_datastream($replacement_strings, 'post', $lom_xml->saveXML());
	}
	
	
	/**
	 * Save the content of the ContentObject in Fedora. 
	 * 
	 * @param $content_object ContentObject
	 * @return boolean Indicates wether the OBJECT datastream could be saved in Fedora
	 */
	protected function save_content_object_datastream($content_object)
	{
	    $data_to_send = $this->get_content_object_content($content_object);
	    $object_id   = $this->get_existing_repository_uid($content_object);
	    
	    $replacement_strings                   = array();
	    $replacement_strings['{pid}']          = $object_id;
	    $replacement_strings['{dsID}']         = self :: DATASTREAM_LO_CONTENT_ID;
	    $replacement_strings['{dsLabel}']      = self :: DATASTREAM_LO_CONTENT_LABEL;
	    $replacement_strings['{controlGroup}'] = 'M';
	    
	    if(is_array($data_to_send) && array_key_exists('mime_type', $data_to_send))
	    {
	        $replacement_strings['{mimeType}']     = $data_to_send['mime_type'];
	    }
	    
	    if(is_array($data_to_send) && array_key_exists('content', $data_to_send))
	    {
	        $datastream_content = $data_to_send['content'];
	    }
	    else
	    {
	        $datastream_content = $data_to_send;
	    }
	    
	    return $this->save_datastream($replacement_strings, 'post', $datastream_content);
	}
	
	
	/**
	 * Update / Create a datastream in Fedora
	 * 
	 * @param $replacement_strings An array of key-value string pairs to replace in the Fedora REST path
	 * @param $http_method The HTTP method to use to send the request to the REST webservice
	 * @param $datastream_content The content of the datastream to save
	 * @return boolean
	 */
	protected function save_datastream($replacement_strings, $http_method, $datastream_content)
	{	    
	    $add_ds_path = $this->get_full_add_datastream_rest_path();
	    
	    if(array_key_exists('{pid}', $replacement_strings))
	    {
	        $add_ds_path = str_replace('{pid}', $replacement_strings['{pid}'], $add_ds_path);
	    }
	    
	    if(array_key_exists('{dsID}', $replacement_strings))
	    {
	        $add_ds_path = str_replace('{dsID}', $replacement_strings['{dsID}'], $add_ds_path);
	    }
	    
	    if(array_key_exists('{dsLabel}', $replacement_strings))
	    {
	        $add_ds_path = str_replace('{dsLabel}', $replacement_strings['{dsLabel}'], $add_ds_path);
	    }
	    
	    if(array_key_exists('{controlGroup}', $replacement_strings))
	    {
	        $add_ds_path = str_replace('{controlGroup}', $replacement_strings['{controlGroup}'], $add_ds_path);
	    }
	    
	    $mime_type = null;
	    if(array_key_exists('{mimeType}', $replacement_strings))
	    {
	        $mime_type = $replacement_strings['{mimeType}'];
	        $add_ds_path = str_replace('{mimeType}', $mime_type, $add_ds_path);
	    }
	    else
        {
            /*
	         * delete mimeType from URL
	         */
	        $add_ds_path = str_replace('&mimeType={mimeType}', '', $add_ds_path);
	        $add_ds_path = str_replace('mimeType={mimeType}', '', $add_ds_path);
        }
	    
	    $response_document = $this->get_rest_xml_response($add_ds_path, $http_method, $datastream_content, $mime_type);
	    
	    //TODO: check what can be a bad response
	    if(isset($response_document))
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Check if the learning object type export is implemented
	 * 
	 * @param $content_object ContentObject
	 * @return boolean
	 */
	protected function check_content_object_is_exportable($content_object)
	{
	    $type = strtolower($content_object->get_type());
	    
	    switch($type)
	    {
	        case 'document':
	            return true;
	            
	        default:
	            return false;
	    }
	}
	
	
	/**
	 * Get the learning object content to export. For a document, it returns a binary file, 
	 * but it may be an XML document, a ZIP file, a SCORM package, etc. depending on the ContentObject type  
	 * 
	 * @param $content_object ContentObject
	 * @return mixed
	 */
	protected function get_content_object_content($content_object)
	{
	    $type = strtolower($content_object->get_type());
	    
	    $content = null;
	    switch($type)
	    {
	        case 'document':
	            return $this->get_document_content($content_object);
	            break;
	    }
	}
	
	
	/**
	 * Get the learning object content to send for the ContentObject type 'Document' 
	 * 
	 * @param $content_object Document
	 * @return unknown_type
	 */
	protected function get_document_content($content_object)
	{
	    $data_to_send              = array();
	    $data_to_send['content']   = file_get_contents($content_object->get_full_path());
	    
	    $mime_type = $this->get_file_mimetype($data_to_send['content']);
	    
	    if(!isset($mime_type))
	    {
	        /*
	         * If the mimetype could not be found by the method above,
	         * get it by using the filename stored in the datasource
	         */
	        $filename  = $content_object->get_filename();
	        $path_info = pathinfo($filename);
	        
	        $mime_type = $this->get_mimetype_from_extension($path_info['extension']);
	    }
	    
	    if(isset($mime_type))
	    {
	        $data_to_send['mime_type'] = $mime_type;
	    }
	    
	    return $data_to_send;
	}
	
	/*************************************************************************/
	
	/**
	 * Returns a new UID generated by a Fedora Repository by using an URL allowing to get it through REST
	 * 
	 * @return mixed A new UID generated by a Fedora repository or false if not URL to retrieve a new UID is set in the configuration
	 * @see chamilo/common/external_export/BaseExternalExporter#get_repository_new_uid()
	 */
	public function get_repository_new_uid()
	{ 
	    $response_document = $this->get_rest_xml_response($this->get_full_get_uid_rest_path(), 'post');
	    
	    if(isset($response_document))
	    {
    		/*
    	     * Find the new uid in the XML
    	     */
    	    $xpath = new DOMXPath($response_document);
    	    $node_list = $xpath->query('/pidList/pid');
    	    if($node_list->length > 0)
    	    {
    	        $new_uid = $node_list->item(0)->nodeValue;
    	        
    	        return $new_uid;
    	    }
    	    else
    	    {
    	        throw new Exception('A new uid could not be retrieved from the Fedora repository');
    	    }
	    }
	    else
	    {
	        throw new Exception('A new uid could not be retrieved from the Fedora repository');
	    }
	}
	
	/*************************************************************************/
	/*** BROWSE / IMPORT functions *******************************************/
	/*************************************************************************/
	
	/**
	 * (non-PHPdoc)
	 * @see repository/lib/export/external_export/BaseExternalExporter#get_objects_list_from_repository()
	 */
	public function get_objects_list_from_repository()
	{
	    $find_objects_path = $this->get_full_find_objects_list_rest_path();
	    $ownerId           = $this->get_user_external_identifier();
	    
	    $find_objects_path = str_replace('{ownerId}', $ownerId, $find_objects_path);
	    
	    //DebugUtilities :: show($find_objects_path);
	     
	    $objects = array();
	    
	    $response_document = $this->get_rest_xml_response($find_objects_path, 'get');
	    if(isset($response_document))
	    {
	        $xpath = new DOMXPath($response_document);
	        $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
	        
	        $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields');
	        if($node_list->length > 0 )
    	    {
    	        foreach ($node_list as $object_node)
    	        {
//    	            DebugUtilities :: show($object_node);
    	            
    	            $object = array();
    	            $object[BaseExternalExporter :: OBJECT_ID]                = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'pid');
    	            $object[BaseExternalExporter :: OBJECT_OWNER_ID]          = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'ownerId');
    	            $object[BaseExternalExporter :: OBJECT_CREATION_DATE]     = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'cDate');
    	            $object[BaseExternalExporter :: OBJECT_MODIFICATION_DATE] = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'mDate');
    	            $object[BaseExternalExporter :: OBJECT_TITLE]             = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'title');
    	            $object[BaseExternalExporter :: OBJECT_DESCRIPTION]       = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'description');
    	            
    	            /*
    	             * Infos specific to Fedora 
    	             */
    	            $object['fedora_state']                                   = XMLUtilities :: get_first_element_value_by_tag_name($object_node, 'state');
    	            
    	            //TODO Calculate the BaseExternalExporter :: OBJECT_SYNC_STATE property
    	            //$object[BaseExternalExporter :: OBJECT_SYNC_STATE]        = ...
    	            
    	            $objects[][BaseExternalExporter :: EXTERNAL_OBJECT_KEY] = $object;
    	        }
    	    }
	    }
	    
	    return $objects;
	}
	
	/*************************************************************************/
	/*** HELPER functions ****************************************************/
	/*************************************************************************/
	
	/**
	 * Return the URL of the REST webservice used to find an object in Fedora 
	 * 
	 * @return string
	 */
	public function get_full_find_object_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_find_object_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
	
	/**
	 * Return the URL of the REST webservice used to save a datastream in Fedora
	 * 
	 * @return string
	 */
	public function get_full_add_datastream_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_add_datastream_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}

	
	/**
	 * Return the URL of the REST webservice used to ingest a new object in Fedora
	 * 
	 * @return string
	 */
	public function get_full_ingest_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_ingest_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
	
	/**
	 * Return the URL of the REST webservice used to get a new uid from Fedora
	 * 
	 * @return string
	 */
	public function get_full_get_uid_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_get_uid_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	} 
	
	/**
	 * Return the URL of the REST webservice used to find the list of existing objects in Fedora 
	 * 
	 * @return string
	 */
	public function get_full_find_objects_list_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_find_objects_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
}
?>