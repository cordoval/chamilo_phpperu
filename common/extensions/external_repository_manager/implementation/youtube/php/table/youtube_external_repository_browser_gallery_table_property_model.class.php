<?php
namespace common\extensions\external_repository_manager\implementation\youtube;
use common\extensions\external_repository_manager\ExternalRepositoryBrowserGalleryPropertyModel;

class YoutubeExternalRepositoryBrowserGalleryPropertyModel extends ExternalRepositoryBrowserGalleryPropertyModel
{

    function __construct()
    {
        parent :: __construct();

        $youtube_properties = YoutubeExternalRepositoryManagerConnector :: get_sort_properties();

        foreach (YoutubeExternalRepositoryManagerConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>