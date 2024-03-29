<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\libraries\GalleryObjectTableProperty;
use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class SoundcloudExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function __construct()
    {
        parent :: __construct();

        $soundcloud_properties = SoundcloudExternalRepositoryManagerConnector :: get_sort_properties();

        foreach (SoundcloudExternalRepositoryManagerConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>