<?php
/**
 * $Id: external_export_fedora.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * @author rodn
 * 
 */
class ExternalExportFedora extends ExternalExport
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_LOGIN = 'login';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_BASE_URL = 'base_url';
    const PROPERTY_GET_UID_REST_PATH = 'get_uid_rest_path';
    const PROPERTY_INGEST_REST_PATH = 'ingest_rest_path';
    const PROPERTY_FINDOBJECT_REST_PATH = 'find_object_rest_path';
    const PROPERTY_FINDOBJECTS_REST_PATH = 'find_objects_rest_path';
    const PROPERTY_ADD_DATASTREAM_REST_PATH = 'add_datastream_rest_path';
    const PROPERTY_CLIENT_CERTIFICATE_FILE = 'client_certificate_file';
    const PROPERTY_CLIENT_CERTIFICATE_KEY_FILE = 'client_certificate_key_file';
    const PROPERTY_CLIENT_CERTIFICATE_KEY_PASSWORD = 'client_certificate_key_password';
    const PROPERTY_TARGET_CA_FILE = 'target_ca_file';

    function ExternalExportFedora($defaultProperties = array ())
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
        $extended_property_names[] = self :: PROPERTY_CLIENT_CERTIFICATE_FILE;
        $extended_property_names[] = self :: PROPERTY_CLIENT_CERTIFICATE_KEY_FILE;
        $extended_property_names[] = self :: PROPERTY_CLIENT_CERTIFICATE_KEY_PASSWORD;
        $extended_property_names[] = self :: PROPERTY_TARGET_CA_FILE;
        
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

    /*************************************************************************/
    
    function get()
    {
        if ($this->is_identified())
        {
            $dm = RepositoryDataManager :: get_instance();
            
            $condition = new EqualityCondition(self :: PROPERTY_ID, $this->get_id());
            
            $result_set = $dm->retrieve_external_export_fedora($condition);
            $object = $result_set->next_result();
            
            if (isset($object))
            {
                $this->set_default_properties($object->get_default_properties());
                
                /*
	             * Add ExternalExport class properties values
	             */
                $external_export = new ExternalExport(array(ExternalExport :: PROPERTY_TYPED_EXTERNAL_EXPORT_ID => $this->get_id()));
                if ($external_export->get_by_typed_external_export_id())
                {
                    foreach (parent :: get_default_property_names() as $property_name)
                    {
                        $this->set_default_property($property_name, $external_export->get_default_property($property_name));
                    }
                }
                else
                {
                    throw new Exception('$external_export->get_by_typed_external_export_id() failed');
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