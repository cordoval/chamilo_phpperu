<?php

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