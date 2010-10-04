<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_property_model.class.php';

class PhotobucketExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function PhotobucketExternalRepositoryGalleryTablePropertyModel()
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