<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\GalleryObjectTableProperty;
use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class YoutubeExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function YoutubeExternalRepositoryGalleryTablePropertyModel()
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