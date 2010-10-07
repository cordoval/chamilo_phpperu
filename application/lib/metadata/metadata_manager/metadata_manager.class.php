<?php
/**
 * @package application.lib.metadata.metadata_manager
 */
require_once dirname(__FILE__).'/../metadata_data_manager.class.php';


/**
 * A metadata manager
 *
 * @author Jens Vanderheyden
 */
 class MetadataManager extends WebApplication
 {
    const APPLICATION_NAME = 'metadata';

    const PARAM_CONTENT_OBJECT = 'content_object';

    const PARAM_METADATA_ATTRIBUTE_NESTING = 'metadata_attribute_nesting';

    const ACTION_EDIT_ASSOCIATIONS = 'metadata_associations_editor';

    const PARAM_METADATA_PROPERTY_NESTING = 'metadata_property_nesting';

    const PARAM_METADATA_NAMESPACE = 'metadata_namespace';

    const ACTION_DELETE_METADATA_NAMESPACE = 'metadata_namespace_deleter';
    const ACTION_EDIT_METADATA_NAMESPACE = 'metadata_namespace_updater';
    const ACTION_CREATE_METADATA_NAMESPACE = 'metadata_namespace_creator';
    const ACTION_BROWSE_METADATA_NAMESPACES = 'metadata_namespaces_browser';

    const PARAM_CONTENT_OBJECT_PROPERTY_METADATA = 'content_object_property_metadata';

    const ACTION_DELETE_CONTENT_OBJECT_PROPERTY_METADATA = 'content_object_property_metadata_deleter';
    const ACTION_EDIT_CONTENT_OBJECT_PROPERTY_METADATA = 'content_object_property_metadata_updater';
    const ACTION_CREATE_CONTENT_OBJECT_PROPERTY_METADATA = 'content_object_property_metadata_creator';
    const ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS = 'content_object_property_metadatas_browser';

    const PARAM_METADATA_PROPERTY_TYPE = 'metadata_property_type';

    const ACTION_DELETE_METADATA_PROPERTY_TYPE = 'metadata_property_type_deleter';
    const ACTION_EDIT_METADATA_PROPERTY_TYPE = 'metadata_property_type_updater';
    const ACTION_CREATE_METADATA_PROPERTY_TYPE = 'metadata_property_type_creator';
    const ACTION_BROWSE_METADATA_PROPERTY_TYPES = 'metadata_property_types_browser';

    const PARAM_METADATA_PROPERTY_VALUE = 'metadata_property_value';

    const ACTION_DELETE_METADATA_PROPERTY_VALUE = 'metadata_property_value_deleter';
    const ACTION_EDIT_METADATA = 'metadata_editor';

    const ACTION_BROWSE_METADATA_PROPERTY_VALUES = 'metadata_property_values_browser';

    const PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE = 'metadata_property_attribute_type';

    const ACTION_DELETE_METADATA_PROPERTY_ATTRIBUTE_TYPE = 'metadata_property_attribute_type_deleter';
    const ACTION_EDIT_METADATA_PROPERTY_ATTRIBUTE_TYPE = 'metadata_property_attribute_type_updater';
    const ACTION_CREATE_METADATA_PROPERTY_ATTRIBUTE_TYPE = 'metadata_property_attribute_type_creator';
    const ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES = 'metadata_property_attribute_types_browser';

    const PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE = 'metadata_property_attribute_value';

    const ACTION_DELETE_METADATA_PROPERTY_ATTRIBUTE_VALUE = 'metadata_property_attribute_value_deleter';

    const ACTION_METADATA_SETTINGS = 'settings';

     const DEFAULT_ACTION = self::ACTION_BROWSE_METADATA_PROPERTY_VALUES;

    /**
     * Constructor
     * @param User $user The current user
	 */
    function MetadataManager($user = null)
    {
    	parent :: __construct($user);
    	//$this->parse_input_from_table();
    }

   
    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action() {
        return self :: DEFAULT_ACTION;

    }
	

    function get_application_name()
    {
            return self :: APPLICATION_NAME;
    }

    function retrieve_metadata_attribute_nestings($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_attribute_nestings($condition, $offset, $count, $order_property);
    }

    function retrieve_metadata_property_nestings($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_nestings($condition, $offset, $count, $order_property);
    }

    function delete_metadata_property_nestings($table_name, $metadata_property_type)
    {
            return MetadataDataManager :: get_instance()->delete_metadata_property_nestings($table_name, $metadata_property_type);
    }

    function delete_metadata_attribute_nestings($table_name, $metadata_property_type)
    {
            return MetadataDataManager :: get_instance()->delete_metadata_attribute_nestings($table_name, $metadata_property_type);
    }

    function retrieve_metadata_attribute_nesting($id)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_attribute_nesting($id);
    }

    function count_metadata_namespaces($condition)
    {
            return MetadataDataManager :: get_instance()->count_metadata_namespaces($condition);
    }

    function retrieve_metadata_namespaces($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_namespaces($condition, $offset, $count, $order_property);
    }

    function retrieve_metadata_namespace($ns_prefix)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_namespace($ns_prefix);
    }

    function count_content_object_property_metadatas($condition)
    {
            return MetadataDataManager :: get_instance()->count_content_object_property_metadatas($condition);
    }

    function retrieve_content_object_property_metadatas($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_content_object_property_metadatas($condition, $offset, $count, $order_property);
    }

    function retrieve_content_object_property_metadata($id)
    {
            return MetadataDataManager :: get_instance()->retrieve_content_object_property_metadata($id);
    }

    function count_metadata_property_types($condition)
    {
            return MetadataDataManager :: get_instance()->count_metadata_property_types($condition);
    }

    function retrieve_metadata_property_types($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_types($condition, $offset, $count, $order_property);
    }

    function retrieve_metadata_property_type($id)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_type($id);
    }

    function count_metadata_property_values($condition)
    {
            return MetadataDataManager :: get_instance()->count_metadata_property_values($condition);
    }

    function retrieve_metadata_property_values($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_values($condition, $offset, $count, $order_property);
    }

    function retrieve_metadata_property_value($id)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_value($id);
    }

    function count_metadata_property_attribute_types($condition)
    {
            return MetadataDataManager :: get_instance()->count_metadata_property_attribute_types($condition);
    }

    function retrieve_metadata_property_attribute_types($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_attribute_types($condition, $offset, $count, $order_property);
    }

    function retrieve_metadata_property_attribute_type($id)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_attribute_type($id);
    }

    function count_metadata_property_attribute_values($condition)
    {
            return MetadataDataManager :: get_instance()->count_metadata_property_attribute_values($condition);
    }

    function retrieve_metadata_property_attribute_values($condition = null, $offset = null, $count = null, $order_property = null)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_attribute_values($condition, $offset, $count, $order_property);
    }

    function retrieve_metadata_property_attribute_value($id)
    {
            return MetadataDataManager :: get_instance()->retrieve_metadata_property_attribute_value($id);
    }

    // Url Creation

    function get_edit_associations_url($metadata_property_type)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_ASSOCIATIONS, self :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id()));
    }

    function get_update_metadata_attribute_nesting_url($metadata_attribute_nesting)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_ATTRIBUTE_NESTING,
                                                                self :: PARAM_METADATA_ATTRIBUTE_NESTING => $metadata_attribute_nesting->get_id()));
    }

    function get_delete_metadata_attribute_nesting_url($metadata_attribute_nesting)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_ATTRIBUTE_NESTING,
                                                                self :: PARAM_METADATA_ATTRIBUTE_NESTING => $metadata_attribute_nesting->get_id()));
    }

    function get_browse_metadata_attribute_nestings_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_ATTRIBUTE_NESTINGS));
    }

    function get_create_metadata_namespace_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_METADATA_NAMESPACE));
    }

    function get_update_metadata_namespace_url($metadata_namespace)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_NAMESPACE, self :: PARAM_METADATA_NAMESPACE => $metadata_namespace->get_ns_prefix()));
    }

    function get_delete_metadata_namespace_url($metadata_namespace)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_NAMESPACE,
                                                                self :: PARAM_METADATA_NAMESPACE => $metadata_namespace->get_ns_prefix()));
    }

    function get_browse_metadata_namespaces_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_NAMESPACES));
    }

    function get_create_content_object_property_metadata_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CONTENT_OBJECT_PROPERTY_METADATA));
    }

    function get_update_content_object_property_metadata_url($content_object_property_metadata)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_CONTENT_OBJECT_PROPERTY_METADATA,
                                                                self :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA => $content_object_property_metadata->get_id()));
    }

    function get_delete_content_object_property_metadata_url($content_object_property_metadata)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CONTENT_OBJECT_PROPERTY_METADATA,
                                                                self :: PARAM_CONTENT_OBJECT_PROPERTY_METADATA => $content_object_property_metadata->get_id()));
    }

    function get_browse_content_object_property_metadatas_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS));
    }

    function get_edit_metadata_property_nesting_url($metadata_property_type)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_PROPERTY_NESTING, self :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id()));
    }

    function get_update_metadata_property_nesting_url($metadata_property_nesting)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_PROPERTY_NESTING,
                                                                self :: PARAM_METADATA_PROPERTY_NESTING => $metadata_property_nesting->get_id()));
    }

    function get_delete_metadata_property_nesting_url($metadata_property_nesting)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_PROPERTY_NESTING,
                                                                self :: PARAM_METADATA_PROPERTY_NESTING => $metadata_property_nesting->get_id()));
    }

    function get_browse_metadata_property_nestings_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_PROPERTY_NESTINGS));
    }

    function get_create_metadata_property_type_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_METADATA_PROPERTY_TYPE));
    }

    function get_update_metadata_property_type_url($metadata_property_type)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_PROPERTY_TYPE,
                                                                self :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id()));
    }

    function get_delete_metadata_property_type_url($metadata_property_type)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_PROPERTY_TYPE,
                                                                self :: PARAM_METADATA_PROPERTY_TYPE => $metadata_property_type->get_id()));
    }

    function get_browse_metadata_property_types_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_PROPERTY_TYPES));
    }

    function get_edit_metadata_property_values_url($content_object)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA,
                                                                self :: PARAM_CONTENT_OBJECT => $content_object->get_id()));
    }

    function get_delete_metadata_property_value_url($metadata_property_value)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_PROPERTY_VALUE,
                                                                self :: PARAM_METADATA_PROPERTY_VALUE => $metadata_property_value->get_id()));
    }

    function get_browse_metadata_property_values_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_PROPERTY_VALUES));
    }

    function get_create_metadata_property_attribute_type_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_METADATA_PROPERTY_ATTRIBUTE_TYPE));
    }

    function get_update_metadata_property_attribute_type_url($metadata_property_attribute_type)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_PROPERTY_ATTRIBUTE_TYPE,
                                                                self :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE => $metadata_property_attribute_type->get_id()));
    }

    function get_delete_metadata_property_attribute_type_url($metadata_property_attribute_type)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_PROPERTY_ATTRIBUTE_TYPE,
                                                                self :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE => $metadata_property_attribute_type->get_id()));
    }

    function get_browse_metadata_property_attribute_types_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES));
    }

    function get_create_metadata_property_attribute_value_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_METADATA_PROPERTY_ATTRIBUTE_VALUE));
    }

    function get_update_metadata_property_attribute_value_url($metadata_property_attribute_value)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_METADATA_PROPERTY_ATTRIBUTE_VALUE,
                                                                self :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE => $metadata_property_attribute_value->get_id()));
    }

    function get_delete_metadata_property_attribute_value_url($metadata_property_attribute_value)
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_METADATA_PROPERTY_ATTRIBUTE_VALUE,
                                                                self :: PARAM_METADATA_PROPERTY_ATTRIBUTE_VALUE => $metadata_property_attribute_value->get_id()));
    }

    function get_browse_url()
    {
            return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    static function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('MetadataNamespacesBrowser'), Translation :: get('MetadataNamespacesDescription'), Theme :: get_image_path() . 'browse_build.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_NAMESPACES)));
        $links[] = new DynamicAction(Translation :: get('MetadataPropertyTypesBrowser'), Translation :: get('MetadataPropertyTypesDescription'), Theme :: get_image_path() . 'browse_build.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_PROPERTY_TYPES)));
        $links[] = new DynamicAction(Translation :: get('MetadataPropertyAttributeTypesBrowser'), Translation :: get('MetadataPropertyAttributeTypesDescription'), Theme :: get_image_path() . 'browse_build.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => self :: ACTION_BROWSE_METADATA_PROPERTY_ATTRIBUTE_TYPES)));
        $links[] = new DynamicAction(Translation :: get('ContentObjectPropertyMetadatasBrowser'), Translation :: get('ContentObjectPropertyMetadatasBrowserDescription'), Theme :: get_image_path() . 'browse_build.png', Redirect :: get_link(self :: APPLICATION_NAME, array(
                Application :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECT_PROPERTY_METADATAS)));
        $info = parent :: get_application_platform_admin_links(self :: APPLICATION_NAME);
        $info['links'] = $links;
        return $info;
    }
}
?>