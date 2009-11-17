<?php
/**
 * $Id: fedora_external_exporter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.external_export.fedora
 */
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
    const DATASTREAM_LOM_NAME = 'LOM';
    const DATASTREAM_LO_CONTENT_NAME = 'OBJECT';
    
    private $base_url = null;
    private $get_uid_rest_path = null;
    private $post_rest_path = null;

    /*************************************************************************/
    
    protected function FedoraExternalExporter($fedora_repository_id = DataClass :: NO_UID)
    {
        parent :: RestExternalExporter($fedora_repository_id);
    }

    /*************************************************************************/
    
    /**
     * (non-PHPdoc)
     * @see chamilo/common/external_export/BaseExternalExporter#export($content_object)
     */
    public function export($content_object)
    {
        if ($this->check_content_object_is_exportable($content_object))
        {
            if ($this->check_required_metadata($content_object))
            {
                $this->prepare_export($content_object);
                
                /*
        	     * Check if the object already exists in Fedora
        	     * - if not, create it
        	     */
                if ($this->check_object_exists($content_object))
                {
                    /*
        	         * Create/Update the LOM-XML datastream in Fedora
        	         */
                    if ($this->save_lom_datastream($content_object))
                    {
                        /*
        	             * Create/Update the learning object datastream in Fedora
        	             */
                        if ($this->save_content_object_datastream($content_object))
                        {
                            return true;
                        }
                        else
                        {
                            throw new Exception('The learning object could not be saved in Fedora');
                        }
                    }
                    else
                    {
                        throw new Exception('The LOM metadata could not be saved in Fedora');
                    }
                }
                else
                {
                    throw new Exception('The object does not exist in Fedora and could not be created');
                }
            }
            else
            {
                Redirect :: url(array(Application :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW, RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => $this->get_external_export()->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
            }
        }
        else
        {
            throw new Exception('This object type can not be exported');
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
        if (isset($response_document))
        {
            /*
	         * Check in the XML if the object exists
	         */
            $xpath = new DOMXPath($response_document);
            $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
            
            $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields/fedora:pid');
            
            if ($node_list->length > 0 && $node_list->item(0)->nodeValue == $object_id)
            {
                return true;
            }
            else
            {
                /*
        	     * Create the object
        	     */
                $ingest_path = $this->get_full_ingest_rest_path();
                $ingest_path = str_replace('{pid}', $object_id, $ingest_path);
                
                $response_document = $this->get_rest_xml_response($ingest_path, 'post');
                
                if (isset($response_document))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            throw new Exception('Unable to check if the object already exists in Fedora');
        }
    }

    /**
     * Create or update the LOM datastream in Fedora with the LOM-XML of the object 
     * @param $content_object ContentObject
     * @return boolean Indicates wether the LOM datastream could be saved in Fedora
     */
    protected function save_lom_datastream($content_object)
    {
        $lom_xml = $this->get_content_object_metadata_xml($content_object);
        $object_id = $this->get_existing_repository_uid($content_object);
        
        $add_ds_path = $this->get_full_add_datastream_rest_path();
        
        $add_ds_path = str_replace('{pid}', $object_id, $add_ds_path);
        $add_ds_path = str_replace('{dsID}', self :: DATASTREAM_LOM_NAME, $add_ds_path);
        $add_ds_path = str_replace('{dsLabel}', self :: DATASTREAM_LOM_NAME, $add_ds_path);
        $add_ds_path = str_replace('{controlGroup}', 'X', $add_ds_path);
        $add_ds_path = str_replace('{mimeType}', 'text/xml', $add_ds_path);
        
        $data_to_send = array();
        $data_to_send['content'] = $lom_xml;
        $data_to_send['mime'] = 'text/xml';
        
        $response_document = $this->get_rest_xml_response($add_ds_path, 'post', $data_to_send);
        
        //TODO: check what can be a bad response
        if (isset($response_document))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 
     * @param $content_object
     * @return boolean
     */
    protected function save_content_object_datastream($content_object)
    {
        $data_to_send = $this->get_content_object_content($content_object);
        
        $object_id = $this->get_existing_repository_uid($content_object);
        
        $add_ds_path = $this->get_full_add_datastream_rest_path();
        $add_ds_path = str_replace('{pid}', $object_id, $add_ds_path);
        $add_ds_path = str_replace('{dsID}', self :: DATASTREAM_LO_CONTENT_NAME, $add_ds_path);
        $add_ds_path = str_replace('{dsLabel}', self :: DATASTREAM_LO_CONTENT_NAME, $add_ds_path);
        $add_ds_path = str_replace('{controlGroup}', 'M', $add_ds_path);
        
        if (isset($data_to_send['file']) && is_array($data_to_send['file']))
        {
            $keys = array_keys($data_to_send['file']);
            if (count($keys) > 0)
            {
                $mime_type = isset($data_to_send['mime_type']) ? $data_to_send['mime_type'] : null;
                
                if (isset($mime_type) && strlen($mime_type) > 0)
                {
                    $add_ds_path = str_replace('{mimeType}', $mime_type, $add_ds_path);
                }
                else
                {
                    /*
        	         * delete mimeType from URL
        	         */
                    $add_ds_path = str_replace('&mimeType={mimeType}', '', $add_ds_path);
                }
            }
        }
        else
        {
            /*
	         * delete mimeType from URL
	         */
            $add_ds_path = str_replace('&mimeType={mimeType}', '', $add_ds_path);
        }
        
        $response_document = $this->get_rest_xml_response($add_ds_path, 'post', $data_to_send);
        
        //TODO: check what can be a bad response
        if (isset($response_document))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if the learning object type export is implemented
     * 
     * @param $content_object ContentObject
     * @return boolean
     */
    protected function check_content_object_is_exportable($content_object)
    {
        $type = strtolower($content_object->get_type());
        
        switch ($type)
        {
            case 'document' :
                return true;
            
            default :
                return false;
        }
    }

    /**
     * Get the learning object content to export
     * 
     * @param $content_object ContentObject
     * @return unknown_type
     */
    protected function get_content_object_content($content_object)
    {
        $type = strtolower($content_object->get_type());
        
        $content = null;
        switch ($type)
        {
            case 'document' :
                return $this->get_document_content($content_object);
                break;
        }
    }

    /**
     * Get the learning object content to send for the type 'Document' 
     * 
     * @param $content_object Document
     * @return unknown_type
     */
    protected function get_document_content($content_object)
    {
        $data_to_send = array();
        $data_to_send['file'] = array(basename($content_object->get_full_path()) => '@' . $content_object->get_full_path());
        
        $mime_type = $this->get_file_mimetype($data_to_send['file']);
        
        if (! isset($mime_type))
        {
            /*
	         * Get the file extension from the filename stored in datasource
	         * and get the corresponding mime type
	         */
            $filename = $content_object->get_filename();
            $path_info = pathinfo($filename);
            
            $mime_type = $this->get_mimetype_from_extension($path_info['extension']);
        }
        
        if (isset($mime_type))
        {
            $data_to_send['mime_type'] = $mime_type;
        }
        
        return $data_to_send;
    }

    /*************************************************************************/
    
    /**
     * @return string
     */
    public function get_full_find_object_rest_path()
    {
        $external_export = $this->get_external_export();
        
        if (isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
        {
            return $external_export->get_full_find_object_rest_path();
        }
        else
        {
            return null;
        }
    }

    /**
     * @return string
     */
    public function get_full_add_datastream_rest_path()
    {
        $external_export = $this->get_external_export();
        
        if (isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
        {
            return $external_export->get_full_add_datastream_rest_path();
        }
        else
        {
            return null;
        }
    }

    /**
     * @return string
     */
    public function get_full_ingest_rest_path()
    {
        $external_export = $this->get_external_export();
        
        if (isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
        {
            return $external_export->get_full_ingest_rest_path();
        }
        else
        {
            return null;
        }
    }

    /**
     * @return string
     */
    public function get_full_get_uid_rest_path()
    {
        $external_export = $this->get_external_export();
        
        if (isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
        {
            return $external_export->get_full_get_uid_rest_path();
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns a new UID generated by a Fedora Repository by using an URL allowing to get it through REST
     * 
     * @return mixed A new UID generated by a Fedora repository or false if not URL to retrieve a new UID is set in the configuration
     * @see chamilo/common/external_export/BaseExternalExporter#get_repository_new_uid()
     */
    public function get_repository_new_uid()
    {
        $response_document = $this->get_rest_xml_response($this->get_full_get_uid_rest_path(), 'post');
        
        if (isset($response_document))
        {
            /*
    	     * Find the new uid in the XML
    	     */
            $xpath = new DOMXPath($response_document);
            $node_list = $xpath->query('/pidList/pid');
            if ($node_list->length > 0)
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

}
?>