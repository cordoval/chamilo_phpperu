<?php
/**
 * $Id: external_repository_fedora.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * @author rodn
 * 
 */
class ExternalRepositoryFedora extends ExternalRepository
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_LOGIN                               = 'login';
    const PROPERTY_PASSWORD                            = 'password';
    const PROPERTY_BASE_URL                            = 'base_url';
    const PROPERTY_GET_UID_REST_PATH                   = 'get_uid_rest_path';
    const PROPERTY_INGEST_REST_PATH                    = 'ingest_rest_path';
    const PROPERTY_FINDOBJECT_REST_PATH                = 'find_object_rest_path';
    const PROPERTY_FINDOBJECTS_REST_PATH               = 'find_objects_rest_path';
    const PROPERTY_ADD_DATASTREAM_REST_PATH            = 'add_datastream_rest_path';
    const PROPERTY_FIND_DATASTREAMS_REST_PATH          = 'find_datastreams_rest_path';
    const PROPERTY_GET_DATASTREAMS_INFOS_PATH          = 'get_datastream_infos_path';
    const PROPERTY_GET_DATASTREAM_CONTENT_PATH         = 'get_datastream_content_path';
    const PROPERTY_CLIENT_CERTIFICATE_FILE             = 'client_certificate_file';
    const PROPERTY_CLIENT_CERTIFICATE_KEY_FILE         = 'client_certificate_key_file';
    const PROPERTY_CLIENT_CERTIFICATE_KEY_PASSWORD     = 'client_certificate_key_password';
    const PROPERTY_TARGET_CA_FILE                      = 'target_ca_file';
    
    const PROPERTY_DUBLIN_CORE_DATASTREAM_NAME         = 'dublin_core_datastream_name';
    const PROPERTY_DUBLIN_CORE_DATASTREAM_LABEL        = 'dublin_core_datastream_label';
    const PROPERTY_EXTENDED_METADATA_DATASTREAM_NAME   = 'extended_metadata_datastream_name';
    const PROPERTY_EXTENDED_METADATA_DATASTREAM_LABEL  = 'extended_metadata_datastream_label';
    const PROPERTY_OBJECT_DATASTREAM_NAME              = 'object_datastream_name';
    const PROPERTY_OBJECT_DATASTREAM_LABEL             = 'object_datastream_label';
    
    const PROPERTY_RELATIONS_DATASTREAM_TEMPLATE       = 'relations_datastream_template';
    
    const DEFAULT_DUBLIN_CORE_DATASTREAM_NAME          = 'DC';
    const DEFAULT_DUBLIN_CORE_DATASTREAM_LABEL         = 'Dublin Core Record for this object';
    const DEFAULT_EXTENDED_METADATA_DATASTREAM_NAME    = 'LOM';
    const DEFAULT_EXTENDED_METADATA_DATASTREAM_LABEL   = 'Learning Object Metadata XML';
    const DEFAULT_OBJECT_DATASTREAM_NAME               = 'OBJECT';
    const DEFAULT_OBJECT_DATASTREAM_LABEL              = 'Object content';
    
    
    function ExternalRepositoryFedora($defaultProperties = array ())
    {
        parent :: __construct($defaultProperties);
    }

    /*************************************************************************/
    
    /**
     * @param $login string Fedora login for actions that need authentication 
     * @return void
     */
    function set_login($login)
    {
        if (isset($login) && strlen($login) > 0)
        {
            $this->set_default_property(self :: PROPERTY_LOGIN, $login);
        }
    }

    function get_login()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN);
    }

    /*************************************************************************/
    
    /**
     * @param $login string Fedora password for actions that need authentication 
     * @return void
     */
    function set_password($password)
    {
        if (isset($password) && strlen($password) > 0)
        {
            $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
        }
    }

    function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
    }

    /*************************************************************************/
    
    /**
     * @param $base_url string The URL to the root of the Fedora repository
     * @return void
     */
    function set_base_url($base_url)
    {
        if (isset($base_url) && strlen($base_url) > 0)
        {
            $base_url = $this->remove_trailing_slash($base_url);
            
            $this->set_default_property(self :: PROPERTY_BASE_URL, $base_url);
        }
    }

    function get_base_url()
    {
        return $this->remove_trailing_slash($this->get_default_property(self :: PROPERTY_BASE_URL));
    }

    /*************************************************************************/
    
    /**
     * Set the path to get a new UID
     * 
     * Note: 	With Fedora 3, the namespace given in the URL must be an alphanumeric string
     * 			e.g: 
     * 				"objects/nextPID?namespace=unigelom&format=xml" is valid
     * 				"objects/nextPID?namespace=unige_lom&format=xml" is not valid
     * 
     * @param $get_uid_path string The path to get a new UID (relative to the root of the Fedora repository)
     * @return void
     */
    function set_get_uid_rest_path($get_uid_rest_path)
    {
        if (isset($get_uid_rest_path))
        {
            $get_uid_rest_path = $this->ensure_start_with_slash($get_uid_rest_path);
            
            $this->set_default_property(self :: PROPERTY_GET_UID_REST_PATH, $get_uid_rest_path);
        }
    }

    function get_get_uid_rest_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_GET_UID_REST_PATH));
    }

    /*************************************************************************/
    
    /**
     * @param $find_object_rest_path string The path to search if an object exists in the repository (relative to the root of the Fedora repository)
     * @return void
     */
    function set_find_object_rest_path($find_object_rest_path)
    {
        if (isset($find_object_rest_path))
        {
            $find_object_rest_path = $this->ensure_start_with_slash($find_object_rest_path);
            
            $this->set_default_property(self :: PROPERTY_FINDOBJECT_REST_PATH, $find_object_rest_path);
        }
    }

    function get_find_object_rest_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_FINDOBJECT_REST_PATH));
    }

	/*************************************************************************/
    
    /**
     * @param $find_objects_rest_path string The path to search existing objects in the repository (relative to the root of the Fedora repository)
     * @return void
     */
    function set_find_objects_rest_path($find_objects_rest_path)
    {
        if (isset($find_objects_rest_path))
        {
            $find_objects_rest_path = $this->ensure_start_with_slash($find_objects_rest_path);
            
            $this->set_default_property(self :: PROPERTY_FINDOBJECTS_REST_PATH, $find_objects_rest_path);
        }
    }

    function get_find_objects_rest_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_FINDOBJECTS_REST_PATH));
    }
    
    /*************************************************************************/
    
    /**
     * @param $ingest_rest_path string The path to add a new object in the repository (relative to the root of the Fedora repository)
     * @return void
     */
    function set_ingest_rest_path($ingest_rest_path)
    {
        if (isset($ingest_rest_path))
        {
            $ingest_rest_path = $this->ensure_start_with_slash($ingest_rest_path);
            
            $this->set_default_property(self :: PROPERTY_INGEST_REST_PATH, $ingest_rest_path);
        }
    }

    function get_ingest_rest_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_INGEST_REST_PATH));
    }

    /*************************************************************************/
    
    /**
     * @param $add_datastream_rest_path string The path to add an object's datastream in the repository (relative to the root of the Fedora repository)
     * @return void
     */
    function set_add_datastream_rest_path($add_datastream_rest_path)
    {
        if (isset($add_datastream_rest_path))
        {
            $add_datastream_rest_path = $this->ensure_start_with_slash($add_datastream_rest_path);
            
            $this->set_default_property(self :: PROPERTY_ADD_DATASTREAM_REST_PATH, $add_datastream_rest_path);
        }
    }

    function get_add_datastream_rest_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_ADD_DATASTREAM_REST_PATH));
    }
    
    
	/*************************************************************************/
    
    /**
     * @param $find_datastreams_rest_path string The path to find an object's datastreams list (relative to the root of the Fedora repository)
     * @return void
     */
    function set_find_datastreams_rest_path($find_datastreams_rest_path)
    {
        if (isset($find_datastreams_rest_path))
        {
            $find_datastreams_rest_path = $this->ensure_start_with_slash($find_datastreams_rest_path);
            
            $this->set_default_property(self :: PROPERTY_FIND_DATASTREAMS_REST_PATH, $find_datastreams_rest_path);
        }
    }

    function get_find_datastreams_rest_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_FIND_DATASTREAMS_REST_PATH));
    }
    
    
	/*************************************************************************/
    
    /**
     * @param $get_datastream_infos_path string The path to get an object's datastream metadata from the repository (relative to the root of the Fedora repository)
     * @return void
     */
    function set_get_datastream_infos_path($get_datastream_infos_path)
    {
        if (isset($get_datastream_infos_path))
        {
            $get_datastream_infos_path = $this->ensure_start_with_slash($get_datastream_infos_path);
            
            $this->set_default_property(self :: PROPERTY_GET_DATASTREAMS_INFOS_PATH, $get_datastream_infos_path);
        }
    }

    function get_get_datastream_infos_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_GET_DATASTREAMS_INFOS_PATH));
    }
    
	/*************************************************************************/
    
    /**
     * @param $get_datastream_content_path string The path to get an object's datastream content from the repository (relative to the root of the Fedora repository)
     * @return void
     */
    function set_get_datastream_content_path($get_datastream_content_path)
    {
        if (isset($get_datastream_content_path))
        {
            $get_datastream_content_path = $this->ensure_start_with_slash($get_datastream_content_path);
            
            $this->set_default_property(self :: PROPERTY_GET_DATASTREAM_CONTENT_PATH, $get_datastream_content_path);
        }
    }

    function get_get_datastream_content_path()
    {
        return $this->ensure_start_with_slash($this->get_default_property(self :: PROPERTY_GET_DATASTREAM_CONTENT_PATH));
    }

    /*************************************************************************/
    
    /**
     * Set an optional client certificate file if a certificate is required to authenticate the REST request (e.g. for Apache server, see http://httpd.apache.org/docs/2.2/ssl/ssl_howto.html#accesscontrol)
     * The filename may be a path relative to the 'ssl' folder in the 'external export' folder.
     * The file may contain the public certificate only, or the public certificate and the private key, all in PEM format.    
     * 
     * Note:
     * 		The private key is a sensitive information and therefore must kept secret. 
     * 		Do not forget to protect the key from unauthorized access, for instance through an .htaccess file 
     * 
     * @param $client_certificate_filename string 
     * @return void
     */
    function set_client_certificate_file($client_certificate_file)
    {
        if (isset($client_certificate_file))
        {
            $this->set_default_property(self :: PROPERTY_CLIENT_CERTIFICATE_FILE, $client_certificate_file);
        }
    }

    function get_client_certificate_file()
    {
        return $this->get_default_property(self :: PROPERTY_CLIENT_CERTIFICATE_FILE);
    }

    /*************************************************************************/
    
    /**
     * Set an optional client certificate key file if a certificate is required to authenticate the REST request (e.g. for Apache server, see http://httpd.apache.org/docs/2.2/ssl/ssl_howto.html#accesscontrol)
     * 
     * Note:
     * 		The private key is a sensitive information and therefore must kept secret. 
     * 		Do not forget to protect the key from unauthorized access, for instance through an .htaccess file
     * 
     * @param $client_certificate_key_filename string
     * @return void
     */
    function set_client_certificate_key_file($client_certificate_key_file)
    {
        if (isset($client_certificate_key_file))
        {
            $this->set_default_property(self :: PROPERTY_CLIENT_CERTIFICATE_KEY_FILE, $client_certificate_key_file);
        }
    }

    function get_client_certificate_key_file()
    {
        return $this->get_default_property(self :: PROPERTY_CLIENT_CERTIFICATE_KEY_FILE);
    }

    /*************************************************************************/
    
    /**
     * Set an optional client certificate key password if a certificate is required to authenticate the REST request (e.g. for Apache server, see http://httpd.apache.org/docs/2.2/ssl/ssl_howto.html#accesscontrol)
     * 
     * @param $client_certificate_key_password string
     * @return void
     */
    function set_client_certificate_key_password($client_certificate_key_password)
    {
        if (isset($client_certificate_key_password))
        {
            $this->set_default_property(self :: PROPERTY_CLIENT_CERTIFICATE_KEY_PASSWORD, $client_certificate_key_password);
        }
    }

    function get_client_certificate_key_password()
    {
        return $this->get_default_property(self :: PROPERTY_CLIENT_CERTIFICATE_KEY_PASSWORD);
    }

    
    /*************************************************************************/
    
    /**
     * Set an optional certificate autority file to check the identity of the target service
     *  
     * @param $target_ca_filename string
     * @return void
     */
    function set_target_ca_file($target_ca_file)
    {
        if (isset($target_ca_file))
        {
            $this->set_default_property(self :: PROPERTY_TARGET_CA_FILE, $target_ca_file);
        }
    }

    function get_target_ca_file()
    {
        return $this->get_default_property(self :: PROPERTY_TARGET_CA_FILE);
    }
    
    
	/*************************************************************************/
    
    /**
     * Set the dublin core datastream name in the Fedora repository
     * Note: if it is not set in the datasource, a default value is used
     * 
     * @param $dublin_core_datastream_name string
     * @return void
     */
    function set_dublin_core_datastream_name($dublin_core_datastream_name)
    {
        if (StringUtilities :: has_value($dublin_core_datastream_name))
        {
            $this->set_default_property(self :: PROPERTY_DUBLIN_CORE_DATASTREAM_NAME, $dublin_core_datastream_name);
        }
    }

    function get_dublin_core_datastream_name()
    {
        $value = $this->get_default_property(self :: PROPERTY_DUBLIN_CORE_DATASTREAM_NAME);
        
        return StringUtilities :: has_value($value) ? $value : self :: DEFAULT_DUBLIN_CORE_DATASTREAM_NAME;
    }
    
    
	/*************************************************************************/
    
    /**
     * Set the dublin core datastream label in the Fedora repository
     * Note: if it is not set in the datasource, a default value is used
     * 
     * @param $dublin_core_datastream_label string
     * @return void
     */
    function set_dublin_core_datastream_label($dublin_core_datastream_label)
    {
        if (StringUtilities :: has_value($dublin_core_datastream_label))
        {
            $this->set_default_property(self :: PROPERTY_DUBLIN_CORE_DATASTREAM_LABEL, $dublin_core_datastream_label);
        }
    }

    function get_dublin_core_datastream_label()
    {
        $value = $this->get_default_property(self :: PROPERTY_DUBLIN_CORE_DATASTREAM_LABEL);
        
        return StringUtilities :: has_value($value) ? $value : self :: DEFAULT_DUBLIN_CORE_DATASTREAM_LABEL;
    }
    
    
    /*************************************************************************/
    
    /**
     * Set the metadata datastream name in the Fedora repository.
     * Note: if it is not set in the datasource, a default value is used
     *  
     * @param $extended_metadata_datastream_name string
     * @return void
     */
    function set_extended_metadata_datastream_name($extended_metadata_datastream_name)
    {
        if (StringUtilities :: has_value($extended_metadata_datastream_name))
        {
            $this->set_default_property(self :: PROPERTY_EXTENDED_METADATA_DATASTREAM_NAME, $extended_metadata_datastream_name);
        }
    }

    function get_extended_metadata_datastream_name()
    {
        $value = $this->get_default_property(self :: PROPERTY_EXTENDED_METADATA_DATASTREAM_NAME);
        
        return StringUtilities :: has_value($value) ? $value : self :: DEFAULT_EXTENDED_METADATA_DATASTREAM_NAME;
    }
    
    
	/*************************************************************************/
    
    /**
     * Set the metadata datastream label in the Fedora repository.
     * Note: if it is not set in the datasource, a default value is used
     *  
     * @param $extended_metadata_datastream_label string
     * @return void
     */
    function set_extended_metadata_datastream_label($extended_metadata_datastream_label)
    {
        if (StringUtilities :: has_value($extended_metadata_datastream_label))
        {
            $this->set_default_property(self :: PROPERTY_EXTENDED_METADATA_DATASTREAM_LABEL, $extended_metadata_datastream_label);
        }
    }

    function get_extended_metadata_datastream_label()
    {
        $value = $this->get_default_property(self :: PROPERTY_EXTENDED_METADATA_DATASTREAM_LABEL);
        
        return StringUtilities :: has_value($value) ? $value : self :: DEFAULT_EXTENDED_METADATA_DATASTREAM_LABEL;
    }
    
    
	/*************************************************************************/
    
    /**
     * Set the object datastream name in the Fedora repository
     * Note: if it is not set in the datasource, a default value is used
     * 
     * @param $object_datastream_name string
     * @return void
     */
    function set_object_datastream_name($object_datastream_name)
    {
        if (StringUtilities :: has_value($object_datastream_name))
        {
            $this->set_default_property(self :: PROPERTY_OBJECT_DATASTREAM_NAME, $object_datastream_name);
        }
    }

    function get_object_datastream_name()
    {
        $value = $this->get_default_property(self :: PROPERTY_OBJECT_DATASTREAM_NAME);
        
        return StringUtilities :: has_value($value) ? $value : self :: DEFAULT_OBJECT_DATASTREAM_NAME;
    }
    
    
	/*************************************************************************/
    
    /**
     * Set the object datastream label in the Fedora repository
     * Note: if it is not set in the datasource, a default value is used
     * 
     * @param $object_datastream_label string
     * @return void
     */
    function set_object_datastream_label($object_datastream_label)
    {
        if (StringUtilities :: has_value($object_datastream_label))
        {
            $this->set_default_property(self :: PROPERTY_OBJECT_DATASTREAM_LABEL, $object_datastream_label);
        }
    }

    function get_object_datastream_label()
    {
        $value = $this->get_default_property(self :: PROPERTY_OBJECT_DATASTREAM_LABEL);
        
        return StringUtilities :: has_value($value) ? $value : self :: DEFAULT_OBJECT_DATASTREAM_LABEL;
    }
    

/*************************************************************************/
    
    /**
     * Set an optional content template for the RELS-EXT datastream
     * Note: if it is not set the RELS-EXT datastream is not created during export
     * 
     * @param $relations_datastream_template string
     * @return void
     */
    function set_relations_datastream_template($relations_datastream_template)
    {
        if (StringUtilities :: has_value($relations_datastream_template))
        {
            $this->set_default_property(self :: PROPERTY_RELATIONS_DATASTREAM_TEMPLATE, $relations_datastream_template);
        }
    }

    function get_relations_datastream_template()
    {
        return $this->get_default_property(self :: PROPERTY_RELATIONS_DATASTREAM_TEMPLATE);
    }
    
    /*************************************************************************/
    
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_LOGIN;
        $extended_property_names[] = self :: PROPERTY_PASSWORD;
        $extended_property_names[] = self :: PROPERTY_BASE_URL;
        $extended_property_names[] = self :: PROPERTY_GET_UID_REST_PATH;
        $extended_property_names[] = self :: PROPERTY_FINDOBJECT_REST_PATH;
        $extended_property_names[] = self :: PROPERTY_FINDOBJECTS_REST_PATH;
        $extended_property_names[] = self :: PROPERTY_INGEST_REST_PATH;
        $extended_property_names[] = self :: PROPERTY_ADD_DATASTREAM_REST_PATH;
        $extended_property_names[] = self :: PROPERTY_FIND_DATASTREAMS_REST_PATH;
        $extended_property_names[] = self :: PROPERTY_GET_DATASTREAMS_INFOS_PATH;
        $extended_property_names[] = self :: PROPERTY_GET_DATASTREAM_CONTENT_PATH;
    
        $extended_property_names[] = self :: PROPERTY_CLIENT_CERTIFICATE_FILE;
        $extended_property_names[] = self :: PROPERTY_CLIENT_CERTIFICATE_KEY_FILE;
        $extended_property_names[] = self :: PROPERTY_CLIENT_CERTIFICATE_KEY_PASSWORD;
        $extended_property_names[] = self :: PROPERTY_TARGET_CA_FILE;
        
        $extended_property_names[] = self :: PROPERTY_DUBLIN_CORE_DATASTREAM_NAME;
        $extended_property_names[] = self :: PROPERTY_DUBLIN_CORE_DATASTREAM_LABEL;
        $extended_property_names[] = self :: PROPERTY_EXTENDED_METADATA_DATASTREAM_NAME;
        $extended_property_names[] = self :: PROPERTY_EXTENDED_METADATA_DATASTREAM_LABEL;
        $extended_property_names[] = self :: PROPERTY_OBJECT_DATASTREAM_NAME;
        $extended_property_names[] = self :: PROPERTY_OBJECT_DATASTREAM_LABEL;
        
        $extended_property_names[] = self :: PROPERTY_RELATIONS_DATASTREAM_TEMPLATE;
        
        return parent :: get_default_property_names($extended_property_names);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    /*************************************************************************/
    
    /**
     * @return string The fully qualified URL to get a new UID from the repository 
     */
    public function get_full_get_uid_rest_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_get_uid_rest_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full get uid path\' is not set');
        }
    }

    /**
     * @return string The fully qualified URL to ingest a new object in the repository 
     */
    public function get_full_ingest_rest_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_ingest_rest_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full ingest path\' is not set');
        }
    }

    /**
     * 
     * @return string
     */
    public function get_full_find_object_rest_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_find_object_rest_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full findObject path\' is not set');
        }
    }
    
	/**
     * 
     * @return string
     */
    public function get_full_find_objects_rest_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_find_objects_rest_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full findObjects path\' is not set');
        }
    }

    /**
     * 
     * @return string
     */
    public function get_full_add_datastream_rest_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_add_datastream_rest_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full add datastream path\' is not set');
        }
    }
    
	/**
     * 
     * @return string
     */
    public function get_full_find_datastreams_rest_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_find_datastreams_rest_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full find datastreams path\' is not set');
        }
    }
    
	/**
     *
     * @return string
     */
    public function get_full_get_datastream_infos_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_get_datastream_infos_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full get datastream infos path\' is not set');
        }
    }
    
	/**
     *
     * @return string
     */
    public function get_full_get_datastream_content_path()
    {
        $base_url = $this->get_base_url();
        $path = $this->get_get_datastream_content_path();
        
        if (isset($base_url) && isset($path))
        {
            return $base_url . $path;
        }
        else
        {
            throw new Exception('Fedora repository \'full get datastream content path\' is not set');
        }
    }
    
    /*************************************************************************/
    
    function get()
    {
        if ($this->is_identified())
        {
            $dm = RepositoryDataManager :: get_instance();
            
            $condition = new EqualityCondition(self :: PROPERTY_ID, $this->get_id());
            
            $result_set = $dm->retrieve_external_repository_fedora($condition);
            $object = $result_set->next_result();
            
            if (isset($object))
            {
                $this->set_default_properties($object->get_default_properties());
                
                /*
	             * Add ExternalRepository class properties values
	             */
                $external_repository = new ExternalRepository(array(ExternalRepository :: PROPERTY_TYPED_EXTERNAL_REPOSITORY_ID => $this->get_id()));
                if ($external_repository->get_by_typed_external_repository_id())
                {
                    foreach (parent :: get_default_property_names() as $property_name)
                    {
                        $this->set_default_property($property_name, $external_repository->get_default_property($property_name));
                    }
                }
                else
                {
                    throw new Exception('$external_repository->get_by_typed_external_repository_id() failed');
                }
                
                return true;
            }
            else
            {
                return false;
            }
        
        }
        else
        {
            return null;
        }
    }

}

?>