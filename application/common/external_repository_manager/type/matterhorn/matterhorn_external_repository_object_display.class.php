<?php

/**
 * 
 * @author magali.gillard
 *
 */
class MatterhornExternalRepositoryObjectDisplay extends StreamingMediaExternalRepositoryObjectDisplay
{
    function get_display_properties()
    {
        $properties = parent :: get_display_properties();
        $properties[Translation :: get('Subject')] = $this->get_object()->get_subject();
        $properties[Translation :: get('Language')] = $this->get_object()->get_language();
        $properties[Translation :: get('Type')] = $this->get_object()->get_type();
        $properties[Translation :: get('Status')] = $this->get_object()->get_status();
        
        
        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $external_repository_instance = RepositoryDataManager :: get_instance()->retrieve_external_repository(Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY));
        $connector = MatterhornExternalRepositoryConnector :: get_instance($external_repository_instance);

        $object = $this->get_object();

        if ($object->get_status() == MatterhornExternalRepositoryObject :: STATUS_AVAILABLE)
        {
			$output = Translation :: get('Available');
        }
        else
        {
            $output = Translation :: get('NotAvailable');
        }
        return $output;
        
    }
}
?>