<?php
require_once dirname(__FILE__) . '/../../../component/external_repository_browser_gallery_table/external_repository_browser_gallery_table_property_model.class.php';
require_once dirname(__FILE__) . '/../matterhorn_external_repository_connector.class.php';

class MatterhornexternalRepositoryBrowserGalleryPropertyModel extends ExternalRepositoryBrowserGalleryPropertyModel
{

    function MatterhornExternalRepositoryBrowserGalleryPropertyModel()
    {
        parent :: __construct();

        foreach (MatterhornExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>