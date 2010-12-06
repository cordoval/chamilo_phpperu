<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\libraries\GalleryObjectTableProperty;
use common\extensions\external_repository_manager\DefaultExternalRepositoryGalleryObjectTablePropertyModel;

class WikimediaExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function __construct()
    {
        parent :: __construct();

        $wikimedia_properties = WikimediaExternalRepositoryManagerConnector :: get_sort_properties();

        foreach (WikimediaExternalRepositoryManagerConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>