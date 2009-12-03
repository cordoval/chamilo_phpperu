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
	            if(!$this->save_content_object_datastream($content_object))
	            {
	                throw new Exception('The object content datastream could not be saved in Fedora');
	            }
	            
	            /**************
	             * Get the last modif date of the Fedora object to store it in Chamilo for future comparison 
	             * 
	             * Note: if you override the export() function, do not forget to call the store_last_repository_update_datetime function again
	             */
	            if(!$this->store_last_repository_update_datetime($content_object, $this->get_existing_repository_uid($content_object)))
	            {
	                throw new Exception('The last modification date could not be stored in Chamilo');
	            }
	            
	            return true;
	        }
	        else
	        {
	            Redirect :: url(array(Application :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW, RepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $this->get_external_export()->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
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
	    $external_object_id = $this->get_existing_repository_uid($content_object);
	    //debug($object_id);
	    
	    $external_object_infos = $this->get_repository_object_infos($external_object_id);
	    
	    if(isset($external_object_infos))
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	    
	    
//	    /*
//	     * Search the object
//	     */
//	    $search_path = $this->get_full_find_object_rest_path();
//	    $search_path = str_replace('{pid}', $object_id, $search_path);
//	    $response_document = $this->get_rest_xml_response($search_path, 'get');
//	    if(isset($response_document))
//	    {
//	        //DebugUtilities :: show($response_document);
//	        
//	        /*
//	         * Check in the XML if the object exists
//	         */
//	        $xpath = new DOMXPath($response_document);
//	        $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
//	        
//    	    $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields/fedora:pid');
//    	    
//    	    //DebugUtilities :: show($node_list);
//    	    
//    	    if($node_list->length > 0 && $node_list->item(0)->nodeValue == $object_id)
//    	    {
//    	        return true;
//    	    }
//	        else
//	        {
//	            return false;
//	        }
//	    }
//	    else
//	    {
//	        throw new Exception('Unable to check if the object already exists in Fedora');
//	    }
	}
	
	
	/**
	 * 
	 * @param string $external_object_id The object identifier in the repository 
	 * @return unknown_type
	 */
	public function get_repository_object_infos($repository_object_id)
	{
		/*
	     * Search the object
	     */
	    $search_path = $this->get_full_find_object_rest_path();
	    $search_path = str_replace('{pid}', $repository_object_id, $search_path);
	    $response_document = $this->get_rest_xml_response($search_path, 'get');
	    if(isset($response_document))
	    {
	        //DebugUtilities :: show($response_document);
	        
	        /*
	         * Check in the XML if the object exists
	         */
	        $xpath = new DOMXPath($response_document);
	        $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
	        
    	    $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields[fedora:pid=\'' . $repository_object_id . '\']');
    	    
    	    //DebugUtilities :: show($node_list);
    	    
    	    if($node_list->length == 1)
    	    {
	            $object_infos = array();
	            
	            foreach($node_list->item(0)->childNodes as $subnode)
	            {
	                if(is_a($subnode, 'DOMElement'))
	                {
	                    switch($subnode->nodeName)
	                    {
	                        case 'pid':
	                            $object_infos[BaseExternalExporter :: OBJECT_ID] = $subnode->nodeValue;
	                            break;
	                        
	                        case 'cDate':
	                            $object_infos[BaseExternalExporter :: OBJECT_CREATION_DATE] = date('Y-m-d H:i:s', strtotime($subnode->nodeValue));
	                            break;
	                        
	                        case 'mDate':
	                            $object_infos[BaseExternalExporter :: OBJECT_MODIFICATION_DATE] = date('Y-m-d H:i:s', strtotime($subnode->nodeValue));
	                            break;
	                        
	                        case 'title':
	                            $object_infos[BaseExternalExporter :: OBJECT_TITLE] = $subnode->nodeValue;
	                            break;
	                        
	                        case 'description':
	                            $object_infos[BaseExternalExporter :: OBJECT_DESCRIPTION] = $subnode->nodeValue;
	                            break;
	                        
	                        default:
	                            $object_infos[$subnode->nodeName] = $subnode->nodeValue;
	                            break;
	                    }
	                    
	                   
	                }
	            }
	            
	            return $object_infos;
    	    }
	        else
	        {
	            return null;
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
	    
	    $response_document = $this->get_rest_response($ingest_path, 'post', $foxml_doc->saveXML(), 'text/xml');
	    
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
	
	/**
	 * - Store the last modification of the object at the time it is exported
	 * - Get the object modification date from Fedora, and store it in the Chamilo datasource
	 * 
	 * @param $content_object ContentObject
	 * @param $repository_object_id string Fedora object uid
	 * @return boolean
	 */
	public function store_last_repository_update_datetime($content_object, $repository_object_id)
	{
	    //$object_id = $this->get_existing_repository_uid($content_object);
	    
	    $search_path = $this->get_full_find_object_rest_path();
	    
	    //$search_path = str_replace('{pid}', $object_id, $search_path);
	    $search_path = str_replace('{pid}', $repository_object_id, $search_path);
	    
	    $response_document = $this->get_rest_xml_response($search_path, 'get');
	    if(isset($response_document))
	    {
	        //DebugUtilities :: show($response_document);
	        
	        /*
	         * Check in the XML if the object exists
	         */
	        $xpath = new DOMXPath($response_document);
	        $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
	        
    	    $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields/fedora:pid');
    	    
    	    //DebugUtilities :: show($node_list);
    	    
    	    //if($node_list->length > 0 && $node_list->item(0)->nodeValue == $object_id)
    	    if($node_list->length > 0 && $node_list->item(0)->nodeValue == $repository_object_id)
    	    {
    	        $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields/fedora:mDate');
    	        if($node_list->length > 0)
    	        {
    	            $external_export = $this->get_external_export();
    	            
    	            $fedora_mDate = $node_list->item(0)->nodeValue;
    	            
    	            //DebugUtilities::show($fedora_mDate);
    	            
    	            $eesi = ExternalExportSyncInfo :: get_by_content_object_and_repository($content_object->get_id(), $external_export->get_id());
    	            
    	            if(!isset($eesi))
    	            {
    	                $eesi = new ExternalExportSyncInfo();
    	                $eesi->set_content_object_id($content_object->get_id());
    	                
    	                $eesi->set_external_repository_id($external_export->get_id());
    	            }
    	            
    	            $eesi->set_external_object_uid($repository_object_id);
    	            
    	            /*
    	             * We store the UTC datetime -> remove the final 'Z' to get a GMT timestamp with strtotime()
    	             */
    	            if(StringUtilities :: end_with($fedora_mDate, 'z', false))
    	            {
    	                $fedora_mDate = substr($fedora_mDate, 0, strlen($fedora_mDate) - 1);
    	            }
    	            
    	            $eesi->set_utc_synchronized(date('Y-m-d H:i:s', strtotime($fedora_mDate)));
    	            
    	            /*
    	             * Store the last modification date of the chamilo object
    	             */
    	            $object_date = $content_object->get_modification_date();
    	            if(!isset($object_date))
    	            {
    	                $object_date = $content_object->get_modification_date();
    	            }
    	            $eesi->set_synchronized_object_datetime($object_date);
    	            
    	            $eesi->save();
    	            
    	            return true;
    	        }
    	        else
    	        {
    	            throw new Exception('The last modification date could not be stored in Chamilo because Fedora did not return any datetime value');
    	        }
    	    }
	        else
	        {
        	    throw new Exception('The last modification date could not be stored in Chamilo because the object does not exist in the Fedora Repository');
	        }
	    }
	    else
	    {
	        throw new Exception('Unable to check if the object already exists in Fedora');
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
    	             * Translate ISO 8601 date to local server time
    	             */
    	            $object[BaseExternalExporter :: OBJECT_CREATION_DATE]     = date('Y-m-d H:i:s', strtotime($object[BaseExternalExporter :: OBJECT_CREATION_DATE]));
    	            $object[BaseExternalExporter :: OBJECT_MODIFICATION_DATE] = date('Y-m-d H:i:s', strtotime($object[BaseExternalExporter :: OBJECT_MODIFICATION_DATE]));
    	            
    	            $objects[][BaseExternalExporter :: EXTERNAL_OBJECT_KEY]   = $object;
    	        }
    	    }
	    }
	    
	    return $objects;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see repository/lib/export/external_export/BaseExternalExporter#import($repository_object_id)
	 */
	public function import($repository_object_id, $owner_id)
	{
	    /*
	     * Get object datastreams
	     */
	    $find_datastreams_rest_path = $this->get_full_find_datastreams_rest_path();
	    $find_datastreams_rest_path = str_replace('{pid}', $repository_object_id, $find_datastreams_rest_path);

	    $response_document = $this->get_rest_xml_response($find_datastreams_rest_path, 'get');
	    
	    if(isset($response_document))
	    {	        
	        $xpath = new DOMXPath($response_document);

	        $ds_list = $xpath->query('/objectDatastreams/datastream');
	        
	        //DebugUtilities :: show($ds_list);
	        
	        if($ds_list->length > 0 )
    	    {
    	        if($this->check_repository_object_is_importable($ds_list))
    	        {
        	        $content_object = $this->get_imported_object_instance($ds_list, $owner_id);
        	        
        	        if(isset($content_object))
        	        {
            	        foreach ($ds_list as $ds_node)
            	        {
            	            $ds_name = XMLUtilities :: get_attribute($ds_node, 'dsid');
            	            
            	            //DebugUtilities :: show($ds_name);
            	            
            	            switch($ds_name)
            	            {
            	                case self :: DATASTREAM_DC_ID:
            	                    $this->set_data_from_dublin_core_datastream($content_object, $ds_node, $repository_object_id);
            	                    break;
            	                    
            	                case self :: DATASTREAM_LO_CONTENT_ID:
            	                    $this->set_data_from_object_datastream($content_object, $ds_node, $repository_object_id);
            	                    break;
            	                    
            	                case self :: DATASTREAM_LOM_ID:
            	                    $this->set_lom_from_object_datastream($content_object, $ds_node, $repository_object_id);
            	                    break;
            	            }
            	        }

            	        //DebugUtilities :: show($content_object);
            	         
            	        return $this->save_imported_content_object($content_object, $repository_object_id);
        	        }
        	        else
        	        {
        	            return false;
        	        }
    	        }
    	        else
    	        {
    	            //TODO: put object type in message
    	            
    	            throw new Exception('Objects of this type can not be imported');
    	        }
    	    }
	    }
	    else
	    {
	        return false;
	    }
	}
	
	private function check_repository_object_is_importable($node_list)
	{
	    $typed_content_object = $this->get_imported_object_instance($node_list);
	    
	    return isset($typed_content_object);
	}
	
	private function get_imported_object_instance($node_list, $owner_id)
	{
	    //DebugUtilities :: show($node_list);
	     
	    $object_node = XmlUtilities :: get_first_element_by_relative_xpath($node_list, '/datastream[@dsid=\'' . self :: DATASTREAM_LO_CONTENT_ID . '\']');
	    
	    if(isset($object_node))
	    {
    	    $mime_type   = XMLUtilities :: get_attribute($object_node, 'mimeType');
    	    
    	    $content_object = null;
    	    
    	    if(StringUtilities :: start_with($mime_type, 'image/', false)
    	         || StringUtilities :: start_with($mime_type, 'application/', false))
    	     {
    	         /*
    	          * Documents can be imported
    	          */
    	         $content_object = new Document();
    	     }
    	     
    	     if(isset($content_object))
    	     {
    	         $content_object->set_owner_id($owner_id);
    	     }
    	     
    	     return $content_object;
	    }
	    else
	    {
	        throw new Exception('No content to import could be found (The object does not have any \'' . self :: DATASTREAM_LO_CONTENT_ID . '\' datastream in Fedora)');
	    }
	}
	
	/**
	 * 
	 * @param ContentObject $content_object Any object inheriting from ContentObject
	 * @param $repository_object_id string Fedora object uid
	 * @return boolean
	 */
	private function save_imported_content_object($content_object, $repository_object_id)
	{
	    $content_object_new_id = DataClass :: NO_UID;
	    
	    switch(get_class($content_object))
	    {
	        case 'Document':
	            $content_object_new_id = $this->save_imported_document($content_object);
	            break;
	        default:
	            throw new Exception('Objects of type \'' . get_class($content_object) . '\' can not be imported');
	            break;
	    }
	    
	    if($content_object_new_id != DataClass :: NO_UID)
	    {
    	    /*
    	     * Save the external object id to be able to recognize the object between Chamilo and the Fedora repository  
    	     */
    	    $lom_mapper      = $this->get_lom_mapper($content_object);
    	    $external_export = $this->get_external_export();
    	    $lom_mapper->add_general_identifier($external_export->get_catalog_name(), $repository_object_id);
    	    $lom_mapper->save_metadata();
    	    
    	    /*
    	     * Save the sync informations
    	     */
    	    return $this->store_last_repository_update_datetime($content_object, $repository_object_id);
	    }
	    else
	    {
	        throw new Exception('The uid of the imported object is undefined');
	    }
	}
	
	/**
	 * 
	 * @param Document $content_object
	 * @return integer The newly created content object id
	 */
	private function save_imported_document($document)
	{
	    if(!$document->save())
        {
            //DebugUtilities :: show($document->get_errors());
            
            throw new Exception($this->build_error_message($document->get_errors()));
        }
        else
        {
            //debug($document->get_id());
            return $document->get_id();
        }
	}
	
	private function build_error_message($error_container)
	{
	    $error_str = '<ul>';
	    
	    if(is_array($error_container))
	    {
	        foreach($error_container as $error_msg)
	        {
	            $error_str .= '<li>' . $error_msg . '</li>';
	        }
	    }
	    else
	    {
	        $error_str .= '<li>' . $error_container . '</li>';
	    }
	    
	    $error_str .= '</ul>';
	    
	    return $error_str;
	}
	
	/**
	 * 
	 * @param ContentObject $content_object
	 * @param DOMNode $ds_node
	 * @param string $repository_object_id
	 * @return void
	 */
	private function set_data_from_dublin_core_datastream(&$content_object, $ds_node, $repository_object_id)
	{
	    $mime_type = XMLUtilities :: get_attribute($ds_node, 'mimeType');

	    if($mime_type == 'text/xml')
	    {
	        $datastream_content_path = $this->get_full_get_datastream_content_path();
	        $datastream_content_path = str_replace('{pid}', $repository_object_id, $datastream_content_path);
	        $datastream_content_path = str_replace('{dsID}', self :: DATASTREAM_DC_ID, $datastream_content_path);
	        
	        $response_document = $this->get_rest_xml_response($datastream_content_path, 'get');
	        if(isset($response_document))
	        {
	            $xpath = new DOMXPath($response_document);
	            $xpath->registerNamespace('oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
	            $xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');
	        
	            /*
	             * Title
	             */
    	        $title_node = $xpath->query('/oai_dc:dc/dc:title');
    	        if($title_node->length == 1)
    	        {
    	            $content_object->set_title($title_node->item(0)->nodeValue);
    	        }
    	        
	        	/*
	             * Description
	             */
    	        $title_node = $xpath->query('/oai_dc:dc/dc:description');
    	        if($title_node->length == 1)
    	        {
    	            $content_object->set_description($title_node->item(0)->nodeValue);
    	        }
	        }
	        else
	        {
	            throw new Exception('Unable to get the Dublin Core metadata document from the reporisory');
	        }
	    }
	}
	
	private function set_data_from_object_datastream(&$content_object, $ds_node, $repository_object_id)
	{
	    switch(get_class($content_object))
	    {
	        case 'Document':
	            $this->set_document_data_from_object_datastream($content_object, $ds_node, $repository_object_id);
	            break;
	            
	        default:
	            throw new Exception('Objects of type \'' . get_class($content_object) . '\' can not be imported');
	            break;
	    }
	}
	
	/**
	 * 
	 * @param Document $content_object
	 * @param DOMNode $ds_node
	 * @param string $repository_object_id
	 * @return void
	 */
	private function set_document_data_from_object_datastream(&$content_object, $ds_node, $repository_object_id)
	{
	    $mime_type = XMLUtilities :: get_attribute($ds_node, 'mimeType');
	    
	    /*
	     * Get the document file content from Fedora
	     */
	    $get_datastream_content_path = $this->get_full_get_datastream_content_path();
	    $get_datastream_content_path = str_replace('{pid}', $repository_object_id, $get_datastream_content_path);
	    $get_datastream_content_path = str_replace('{dsID}', self :: DATASTREAM_LO_CONTENT_ID, $get_datastream_content_path);
	    
	    $response_content = $this->get_rest_response($get_datastream_content_path, 'get');
	    
	    $content_object->set_in_memory_file($response_content);
	    
	    $filename = $repository_object_id;
	    
	    switch($mime_type)
	    {
	        case 'image/jpeg':
	            $filename .= '.jpeg';
	            break;
	            
	        case 'image/gif':
	            $filename .= '.gif';
	            break;
	            
	        case 'image/png':
	            $filename .= '.png';
	            break;
	            
	        case 'image/tiff':
	            $filename .= '.tiff';
	            break;
	            
	        case 'image/bmp':    
	            $filename .= '.bmp';
	            break;
	            
	        case 'application/pdf':    
	            $filename .= '.pdf';
	            break;
	        
	        case 'application/word':    
	            $filename .= '.doc';
	            break;
	        
	        case 'application/excel':    
	            $filename .= '.xls';
	            break;
	        
	        case 'application/powerpoint':    
	            $filename .= '.ppt';
	            break;

	        case 'application/vnd.oasis.opendocument.text':    
	            $filename .= '.odt';
	            break;
	
            case 'application/vnd.oasis.opendocument.text-template':    
	            $filename .= '.ott';
	            break;
	            
            case 'application/vnd.oasis.opendocument.text-web':    
	            $filename .= '.oth';
	            break;
            	
            case 'application/vnd.oasis.opendocument.text-master':    
	            $filename .= '.odm';
	            break;
            	
            case 'application/vnd.oasis.opendocument.graphics':    
	            $filename .= '.odg';
	            break;
            	
            case 'application/vnd.oasis.opendocument.graphics-template':    
	            $filename .= '.otg';
	            break;
            
            case 'application/vnd.oasis.opendocument.presentation':    
	            $filename .= '.odp';
	            break;
            	
            case 'application/vnd.oasis.opendocument.presentation-template':    
	            $filename .= '.otp';
	            break;
            	
            case 'application/vnd.oasis.opendocument.spreadsheet':    
	            $filename .= '.ods';
	            break;
            	
            case 'application/vnd.oasis.opendocument.spreadsheet-template':    
	            $filename .= '.ots';
	            break;
            	
            case 'application/vnd.oasis.opendocument.chart':    
	            $filename .= '.odc';
	            break;
            	
            case 'application/vnd.oasis.opendocument.formula':    
	            $filename .= '.odf';
	            break;
            	
            case 'application/vnd.oasis.opendocument.database':    
	            $filename .= '.odb';
	            break;
            	
            case 'application/vnd.oasis.opendocument.image':    
	            $filename .= '.odi';
	            break;
            	
            case 'application/vnd.openofficeorg.extension':    
	            $filename .= '.oxt';
	            break;
	    }
	    
	    $content_object->set_filename(Filesystem :: create_safe_name($filename));
	}
	
	private function set_lom_from_object_datastream(&$content_object, $ds_node, $repository_object_id)
	{
	    $mime_type = XMLUtilities :: get_attribute($ds_node, 'mimeType');
	    
//	    DebugUtilities :: show($mime_type);

	    
	    
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
	
	public function get_full_find_datastreams_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_find_datastreams_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
	public function get_full_get_datastream_infos_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_get_datastream_infos_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
	public function get_full_get_datastream_content_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_get_datastream_content_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
}
?>