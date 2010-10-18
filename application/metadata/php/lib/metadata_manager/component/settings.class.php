<?php
class MetadataManagerSettingscomponent extends MetadataManager
{
    function run()
    {
        $this->display_header($trail);

        echo '<br /><a href="' . $this->get_browse_metadata_namespaces_url() . '">' . Translation :: get('BrowseMetadataNamespaces') . '</a>';
	echo '<br /><a href="' . $this->get_browse_content_object_property_metadatas_url() . '">' . Translation :: get('BrowseContentObjectPropertyMetadatas') . '</a>';
	echo '<br /><a href="' . $this->get_browse_metadata_property_types_url() . '">' . Translation :: get('BrowseMetadataPropertyTypes') . '</a>';
	echo '<br /><a href="' . $this->get_browse_metadata_property_attribute_types_url() . '">' . Translation :: get('BrowseMetadataPropertyAttributeTypes') . '</a>';
	
        $this->display_footer();
    }
}

?>
