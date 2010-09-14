<?php
require_once dirname(__FILE__) . '/../../../component/external_repository_browser_gallery_table/external_repository_browser_gallery_table_property_model.class.php';
require_once dirname(__FILE__) . '/../drop_io_external_repository_connector.class.php';

class DropIoExternalRepositoryBrowserGalleryPropertyModel extends ExternalRepositoryBrowserGalleryPropertyModel
{

    function DropIoExternalRepositoryBrowserGalleryPropertyModel()
    {
        parent :: __construct();

        foreach (DropIoExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>