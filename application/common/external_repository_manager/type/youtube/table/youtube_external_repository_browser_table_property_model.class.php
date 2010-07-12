<?php
require_once dirname(__FILE__) . '/../../../component/external_repository_browser_table/external_repository_browser_table_property_model.class.php';
require_once dirname(__FILE__) . '/../youtube_external_repository_connector.class.php';

class YoutubeExternalRepositoryBrowserPropertyModel extends ExternalRepositoryBrowserPropertyModel
{

    function YoutubeExternalRepositoryBrowserPropertyModel()
    {
        parent :: __construct();
        
        $youtube_properties = YoutubeExternalRepositoryConnector :: get_sort_properties();
        
        foreach (YoutubeExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>