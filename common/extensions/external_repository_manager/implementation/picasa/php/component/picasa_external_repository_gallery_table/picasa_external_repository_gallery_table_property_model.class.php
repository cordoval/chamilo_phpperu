<?php
namespace common\extensions\external_repository_manager\implementation\picasa;

use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class PicasaExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function PicasaExternalRepositoryGalleryTablePropertyModel()
    {
        parent :: __construct();

        $picasa_properties = PicasaExternalRepositoryConnector :: get_sort_properties();

        foreach (PicasaExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>