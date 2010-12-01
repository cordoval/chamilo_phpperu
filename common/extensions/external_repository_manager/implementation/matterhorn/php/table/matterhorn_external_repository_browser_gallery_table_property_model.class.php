<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;
use common\extensions\external_repository_manager\ExternalRepositoryBrowserGalleryPropertyModel;

require_once dirname(__FILE__) . '/../matterhorn_external_repository_manager_connector.class.php';

class MatterhornExternalRepositoryBrowserGalleryPropertyModel extends ExternalRepositoryBrowserGalleryPropertyModel
{

    function MatterhornExternalRepositoryBrowserGalleryPropertyModel()
    {
        parent :: __construct();

        foreach (MatterhornExternalRepositoryManagerConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>