<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_property_model.class.php';

class FlickrExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function FlickrExternalRepositoryGalleryTablePropertyModel()
    {
        parent :: __construct();

        $flickr_properties = FlickrExternalRepositoryConnector :: get_sort_properties();

        foreach (FlickrExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>