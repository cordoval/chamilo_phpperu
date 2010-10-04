<?php
require_once Path :: get_repository_path() . '/lib/export/external_export/fedora/fedora_external_repository.class.php';

/**
 * This class is an example of a custom implementation of an export to an external Fedora repository.
 * Such a custom implementation is a way to write a class handling specific business logic that must be done during an export.
 * 
 * The class is returned by the BaseExternalRepositoryConnector :: get_instance() method instead of the generic one (which would be FedoraExternalRepositoryConnector in this case) if its name start with the 
 * camelized version of the catalog_name field value of the repository_external_repository table in the datasource.
 * 
 * E.g: in order to have this specific example class called, set the value of the catalog_name field to 'fedora_test' and the type field to 'fedora'.
 * 		Then, instead of loading an instance of FedoraExternalRepositoryConnector, an instance of FedoraTestExternalRepositoryConnector is returned by BaseExternalRepositoryConnector :: get_instance().
 * 
 * 
 * In this test example, a datastream called 'ANIMAL' is created with the value choosed in the 'FedoraTestExternalExportForm' form, 
 * and the right description is mandatory in the metadata 
 * 
 */
class FedoraTestExternalRepositoryConnector extends FedoraExternalRepositoryConnector
{
    const DATASTREAM_ANIMAL = 'ANIMAL';

    function FedoraTestExternalRepositoryConnector($fedora_repository_id = DataClass :: NO_UID)
    {
        parent :: FedoraExternalRepositoryConnector($fedora_repository_id);
    }

    /**
     * (non-PHPdoc)
     * @see chamilo/common/external_export/fedora/FedoraExternalRepositoryConnector#export($content_object)
     */
    public function export($content_object)
    {
        if (parent :: export($content_object))
        {
            /*
	         * Create the datastream specific to the Unige learning object repository
	         * It will contain the LO's owner AAI unique id and the SWITCHcollection access right 
	         */
            if ($this->save_animal_datastream($content_object))
            {
                return true;
            }
            else
            {
                throw new Exception('The animal datastream could not be saved in Fedora');
            }
        }
    }

    /**
     * 
     * @param $content_object ContentObject
     * @return boolean
     */
    private function save_animal_datastream($content_object)
    {
        $animal_doc = $this->create_animal_document($content_object); //$chor_doc->saveXML();
        $object_id = $this->get_existing_repository_uid($content_object);
        
        $add_ds_path = $this->get_full_add_datastream_rest_path();
        
        $add_ds_path = str_replace('{pid}', $object_id, $add_ds_path);
        $add_ds_path = str_replace('{dsID}', self :: DATASTREAM_ANIMAL, $add_ds_path);
        $add_ds_path = str_replace('{dsLabel}', self :: DATASTREAM_ANIMAL, $add_ds_path);
        $add_ds_path = str_replace('{controlGroup}', 'X', $add_ds_path);
        $add_ds_path = str_replace('{mimeType}', 'text/xml', $add_ds_path);
        
        $response_document = $this->get_rest_xml_response($add_ds_path, 'post', $animal_doc->saveXML());
        
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
     * Create a document equivalent to the CHOR_DC datastream content in the SWITCH repository
     * 
     * @param $content_object ContentObject
     * @return DOMDocument
     */
    private function create_animal_document($content_object)
    {
        $animal_doc = new DOMDocument();
        $animals_node = $animal_doc->createElement('animals');
        $animal_doc->appendChild($animals_node);
        
        /*
	     * Value selected in the 'FedoraTestExternalExportForm' form
	     */
        $animal_value = Session :: retrieve('external_repository.fedora_test.animal');
        
        if (isset($animal_value))
        {
            $animal_node = $animal_doc->createElement('animal');
            $animal_node->nodeValue = $animal_value;
            $animals_node->appendChild($animal_node);
        }
        
        return $animal_doc;
    }

    /**
     * (non-PHPdoc)
     * @see chamilo/common/external_export/BaseExternalRepositoryConnector#check_required_metadata($content_object)
     */
    public function check_required_metadata($content_object)
    {
        /*
	     * For this test export, the right description is required
	     */
        
        $has_missing_fields = false;
        
        $this->clear_missing_fields();
        
        $lom_mapper = $this->get_lom_mapper($content_object);
        
        $right_descriptions = $lom_mapper->get_rights_description();
        if (count($right_descriptions->get_strings()) == 0)
        {
            /*
	         * A right description is mandatory
	         */
            $this->store_missing_fields($content_object->get_id(), 'rights.description');
            $has_missing_fields = true;
        }
        
        return ! $has_missing_fields;
    }

    private function clear_missing_fields()
    {
        Session :: unregister(BaseExternalRepositoryConnector :: SESSION_MISSING_FIELDS);
    }

    private function store_missing_fields($content_object_id, $fieldname)
    {
        $missing_infos = Session :: retrieve(BaseExternalRepositoryConnector :: SESSION_MISSING_FIELDS);
        
        if (! isset($missing_infos))
        {
            $missing_infos = array();
        }
        
        if (! isset($missing_infos[$fieldname]))
        {
            $missing_infos[$fieldname] = array();
            $missing_infos[$fieldname]['fields'] = array();
        }
        
        $message = null;
        switch ($fieldname)
        {
            case 'rights.description' :
                $message = 'A right description is mandatory in order to export the learning object to the UniGE repository';
                break;
        }
        
        if (isset($message))
        {
            $missing_infos[$fieldname]['message'] = $message;
        }
        
        $missing_infos[$fieldname]['fields'][] = '';
        
        Session :: register(BaseExternalRepositoryConnector :: SESSION_MISSING_FIELDS, $missing_infos);
    }

}
?>