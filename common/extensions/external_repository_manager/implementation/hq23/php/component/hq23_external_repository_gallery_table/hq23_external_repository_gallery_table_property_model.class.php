<?php
namespace common\extensions\external_repository_manager\implementation\hq23;

use common\extensions\external_repository_manager\ExternalRepositoryConnector;
use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class Hq23ExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function __construct()
    {
        parent :: __construct();

        $hq23_properties = Hq23ExternalRepositoryConnector :: get_sort_properties();

        foreach (Hq23ExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>