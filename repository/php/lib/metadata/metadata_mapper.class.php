<?php
/**
 * $Id: metadata_mapper.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata
 */
require_once Path :: get_common_path() . 'xml/xml_utilities.class.php';
require_once Path :: get_common_path() . 'string/string_utilities.class.php';

abstract class MetadataMapper
{
    const ORIGINAL_ID_ATTRIBUTE = 'original_id';
    const OVERRIDE_ID_ATTRIBUTE = 'override_id';
    const METADATA_ID_ATTRIBUTE = 'metadata_id';
    const METADATA_OVERRIDE_ID = ContentObjectMetadata :: PROPERTY_OVERRIDE_ID;

    protected $content_object;
    protected $additional_metadata_array;
    protected $repository_data_manager;
    protected $errors;

    /**
     *
     * @param mixed ContentObject id or a ContentObject instance
     */
    function MetadataMapper($content_object)
    {
        $this->errors = array();

        if (isset($content_object))
        {
            $this->repository_data_manager = RepositoryDataManager :: get_instance();

            if (is_numeric($content_object))
            {
                $lo = $this->repository_data_manager->retrieve_content_object($content_object);
                if (isset($lo))
                {
                    $this->content_object = $lo;
                }
                else
                {
                    throw new Exception('content_object could not be retrieved while creating an IeeeLomMapper');
                }
            }
            elseif (is_a($content_object, 'ContentObject'))
            {
                $this->content_object = $content_object;
            }
            else
            {
                throw new Exception('Not able to create MetadataMapper. Wrong type parameters.');
            }
        }
        else
        {
            throw new Exception('Unable to create a MetadataMapper without any content_object');
        }
    }

    /****************************************************************************************/

    /**
     * Get the metadata from the metadata table for a specific learning object
     *
     * @param ContentObject $content_object
     * @return array of ContentObjectMetadata
     */
    protected function retrieve_content_object_additional_metadata($content_object)
    {
        $id = $content_object->get_id();
        $conditions = new EqualityCondition(ContentObjectMetadata :: PROPERTY_CONTENT_OBJECT, $id);

        $additional_metadata = $this->repository_data_manager->retrieve_content_object_metadata($conditions, null, null, null);
        $additional_metadata_array = array();
        while ($metadata = $additional_metadata->next_result())
        {
            $additional_metadata_array[] = $metadata;
        }

        return $additional_metadata_array;
    }

    /**
     * Get existing metadata from the metadata table for a specific metadata field and type
     *
     * @param $field_name The name of the datasource column (e.g. 'property')
     * @param $start_like The beginning of the metadata field name (e.g. 'general_title[')
     * @param $metadata_type The type of Metadata (e.g. 'LOMV1.0')
     * @return ObjectResultSet
     */
    protected function retrieve_existing_metadata($field_name, $start_like, $metadata_type)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectMetadata :: PROPERTY_CONTENT_OBJECT, $this->content_object->get_id());
        $conditions[] = new EqualityCondition(ContentObjectMetadata :: PROPERTY_TYPE, $metadata_type);
        $conditions[] = new PatternMatchCondition($field_name, $start_like . '*');
        $condition = new AndCondition($conditions);

        $existing_metadata = $this->repository_data_manager->retrieve_content_object_metadata($condition);

        return $existing_metadata;
    }

    /**
     * Get an array of existing metadata id from the metadata table for a specific metadata field and type
     *
     * @param $field_name The name of the datasource column (e.g. 'property')
     * @param $start_like The beginning of the metadata field name (e.g. 'general_title[')
     * @param $metadata_type The type of Metadata (e.g. 'LOMV1.0')
     * @return array of id
     */
    protected function retrieve_existing_metadata_id($field_name, $start_like, $metadata_type)
    {
        $existing_metadata = $this->retrieve_existing_metadata($field_name, $start_like, $metadata_type);

        $existing_metadata_id = array();

        while ($ex_meta = $existing_metadata->next_result())
        {
            $existing_metadata_id[$ex_meta->get_id()] = $ex_meta;
        }

        return $existing_metadata_id;
    }

    /**
     * Compare two arrays of ids. For each existing id not saved, delete the existing metadata
     * record in the datasource
     *
     * @param $existing_metadata_id Array of existing metadata ids
     * @param $saved_metadata_id Array of saved metadata id (= the metadata record ids to keep in datasource)
     */
    protected function delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id)
    {
        /*
         * Remove metadata that were sent back for saving
         */
        //debug($existing_metadata_id);
        //debug($saved_metadata_id);


        foreach ($saved_metadata_id as $saved_id => $meta)
        {
            if (array_key_exists($saved_id, $existing_metadata_id))
            {
                unset($existing_metadata_id[$saved_id]);
            }
        }

        /*
         * Delete the existing metadata that were not saved
         */
        foreach ($existing_metadata_id as $saved_id => $metadata_to_delete)
        {
            try
            {
                $metadata_to_delete->delete();
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
    }

    /**
     * Filter the already retrieved existing metadata
     *
     * @param $filter The beginning of the property name to filter on
     * @return array of ContentObjectMetadata
     */
    protected function get_additional_metadata($filter)
    {
        if (isset($filter))
        {
            $filtered_metadata = array();

            foreach ($this->additional_metadata_array as $metadata)
            {
                if (StringUtilities :: start_with($metadata->get_property(), $filter))
                {
                    $filtered_metadata[] = $metadata;
                }
            }

            return $filtered_metadata;
        }
    }

    /**
     * Return the ContentObject instance
     *
     * @return ContentObject
     */
    public function get_content_object()
    {
        return $this->content_object;
    }

    /**
     * Return a new ContentObjectMetadata instance with the given properties
     *
     * @return ContentObjectMetadata
     */
    protected function get_new_content_object_metadata($id = null, $type = null, $property = null, $value = null, $override_id = null)
    {
        //        debug($property);
        //        debug($value);


        $metaData = new ContentObjectMetadata();
        $metaData->set_content_object_id($this->content_object->get_id());
        $metaData->set_type($type);

        if (isset($id))
        {
            $metaData->set_id($id);
        }

        if (isset($property))
        {
            $metaData->set_property($property);
        }

        $metaData->set_value($value);

        if (isset($override_id) && strlen($override_id) > 0 && is_numeric($override_id) && $override_id != DataClass :: NO_UID)
        {
            $metaData->set_override_id($override_id);
        }

        return $metaData;
    }

    /**
     * Add an error message to the collection of errors
     *
     * @return $error_msg The new error message
     */
    protected function add_error($error_msg)
    {
        $this->errors[] = array('message' => $error_msg);
    }

    /**
     * Indicates wether the collection of errors contains elements
     *
     * @return bool
     */
    public function has_error()
    {
        return (count($this->errors) > 0);
    }

    /**
     * Return an HTML formatted list of errors
     * @return string HTML list
     */
    public function get_errors_as_html()
    {
        if (count($this->errors) > 0)
        {
            $error_str = '<ul>';

            foreach ($this->errors as $error)
            {
                $error_str .= '<li>' . $error['message'] . '</li>';
            }

            $error_str .= '</ul>';

            return $error_str;
        }
    }

    /**
     * Merge the default metadata retrieved form the ContentObject properties
     * with the metadata stored in the metadata table of the datasource
     *
     * @param $additional_metadata Array of ContentObjectMetadata
     */
    abstract function merge_additional_metadata($additional_metadata);

    /**
     * Generates the metadata for the setted ContentObject and return it as an object
     *
     * @return mixed Object
     */
    abstract function get_metadata();

    /**
     * Generates the metadata for the setted ContentObject and print it in the page
     *
     * @param $format_for_html_page boolean Indicates wether the printed metadata must be formatted to be included in a HTML page
     * @param $return_document bolean Indicates wether the generated document must be printed in the response page or returned
     * @return void
     */
    abstract function export_metadata($format_for_html_page = false, $return_document = false);

}
?>