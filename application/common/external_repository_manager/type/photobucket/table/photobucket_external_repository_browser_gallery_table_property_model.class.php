<?php
require_once dirname(__FILE__) . '/../../../component/external_repository_browser_gallery_table/external_repository_browser_gallery_table_property_model.class.php';
require_once dirname(__FILE__) . '/../photobucket_external_repository_connector.class.php';

class PhotobucketExternalRepositoryBrowserGalleryPropertyModel extends ExternalRepositoryBrowserGalleryPropertyModel
{

    function PhotobucketExternalRepositoryBrowserGalleryPropertyModel()
    {
        parent :: __construct();

//        $youtube_properties = PhotobucketExternalRepositoryConnector :: get_sort_properties();
//
//        foreach (PhotobucketExternalRepositoryConnector :: get_sort_properties() as $property)
//        {
//            $this->add_property(new GalleryObjectTableProperty($property));
//        }
    }
}
?>