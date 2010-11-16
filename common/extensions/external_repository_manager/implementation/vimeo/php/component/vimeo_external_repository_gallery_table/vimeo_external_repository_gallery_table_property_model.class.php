<?php
namespace common\extensions\external_repository_manager\implementation\vimeo;

use common\libraries\GalleryObjectTableProperty;
use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class VimeoExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function VimeoExternalRepositoryGalleryTablePropertyModel()
    {
        parent :: __construct();

        $flickr_properties = VimeoExternalRepositoryConnector :: get_sort_properties();

        foreach (VimeoExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>