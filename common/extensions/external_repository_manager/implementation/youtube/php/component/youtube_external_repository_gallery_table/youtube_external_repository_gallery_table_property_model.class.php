<?php
namespace common\extensions\external_repository_manager\implementation\youtube;
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