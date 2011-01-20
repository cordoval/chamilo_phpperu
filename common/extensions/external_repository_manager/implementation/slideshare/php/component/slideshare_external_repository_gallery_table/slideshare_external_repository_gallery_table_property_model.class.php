<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\libraries\GalleryObjectTableProperty;
use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class SlideshareExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function __construct()
    {
        parent :: __construct();

        $flickr_properties = SlideshareExternalRepositoryManagerConnector :: get_sort_properties();

        foreach (SlideshareExternalRepositoryManagerConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>