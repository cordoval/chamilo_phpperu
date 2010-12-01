<?php
namespace common\extensions\external_repository_manager\implementation\picasa;

use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class PicasaExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function __construct()
    {
        parent :: __construct();

        $picasa_properties = PicasaExternalRepositoryManagerConnector :: get_sort_properties();

        foreach (PicasaExternalRepositoryManagerConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>