<?php
/**
 * $Id: dokeos185_openid_association.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_openid_association.class.php';

/**
 * This class presents a Dokeos185 openid_association
 *
 * @author Sven Vanpoucke
 */
class Dokeos185OpenidAssociation extends MigrationDataClass
{
    private static $mgdm;
    
    /**
     * Dokeos185OpenidAssociation properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_IDP_ENDPOINT_URI = 'idp_endpoint_uri';
    const PROPERTY_SESSION_TYPE = 'session_type';
    const PROPERTY_ASSOC_HANDLE = 'assoc_handle';
    const PROPERTY_ASSOC_TYPE = 'assoc_type';
    const PROPERTY_EXPIRES_IN = 'expires_in';
    const PROPERTY_MAC_KEY = 'mac_key';
    const PROPERTY_CREATED = 'created';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185OpenidAssociation object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185OpenidAssociation($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_IDP_ENDPOINT_URI, self :: PROPERTY_SESSION_TYPE, self :: PROPERTY_ASSOC_HANDLE, self :: PROPERTY_ASSOC_TYPE, self :: PROPERTY_EXPIRES_IN, self :: PROPERTY_MAC_KEY, self :: PROPERTY_CREATED);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this Dokeos185OpenidAssociation.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the idp_endpoint_uri of this Dokeos185OpenidAssociation.
     * @return the idp_endpoint_uri.
     */
    function get_idp_endpoint_uri()
    {
        return $this->get_default_property(self :: PROPERTY_IDP_ENDPOINT_URI);
    }

    /**
     * Returns the session_type of this Dokeos185OpenidAssociation.
     * @return the session_type.
     */
    function get_session_type()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_TYPE);
    }

    /**
     * Returns the assoc_handle of this Dokeos185OpenidAssociation.
     * @return the assoc_handle.
     */
    function get_assoc_handle()
    {
        return $this->get_default_property(self :: PROPERTY_ASSOC_HANDLE);
    }

    /**
     * Returns the assoc_type of this Dokeos185OpenidAssociation.
     * @return the assoc_type.
     */
    function get_assoc_type()
    {
        return $this->get_default_property(self :: PROPERTY_ASSOC_TYPE);
    }

    /**
     * Returns the expires_in of this Dokeos185OpenidAssociation.
     * @return the expires_in.
     */
    function get_expires_in()
    {
        return $this->get_default_property(self :: PROPERTY_EXPIRES_IN);
    }

    /**
     * Returns the mac_key of this Dokeos185OpenidAssociation.
     * @return the mac_key.
     */
    function get_mac_key()
    {
        return $this->get_default_property(self :: PROPERTY_MAC_KEY);
    }

    /**
     * Returns the created of this Dokeos185OpenidAssociation.
     * @return the created.
     */
    function get_created()
    {
        return $this->get_default_property(self :: PROPERTY_CREATED);
    }

    /**
     * Check if the open id association is valid
     * @param array $array the parameters for the validation
     * @return true if the open id association is valid 
     */
    function is_valid($array)
    {
    
    }

    /**
     * Convert to new open id association
     * @param array $array the parameters for the conversion
     * @return the new open id association
     */
    function convert_data
    {
    
    }

    /**
     * Retrieve all open id associations from the database
     * @param array $parameters parameters for the retrieval
     * @return array of open id associations
     */
    static function retrieve_data($parameters)
    {
        self :: $mgdm = $parameters['mgdm'];
        
        $db = 'main_database';
        $tablename = 'openid_association';
        $classname = 'Dokeos185OpenidAssociation';
        
        return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = 'main_database';
        $array['table'] = 'openid_association';
        return $array;
    }
}

?>