<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;
use common\extensions\external_repository_manager\ExternalRepositoryBrowserGalleryPropertyModel;

require_once dirname(__FILE__) . '/../matterhorn_external_repository_connector.class.php';

class MatterhornexternalRepositoryBrowserGalleryPropertyModel extends ExternalRepositoryBrowserGalleryPropertyModel
{

    function MatterhornExternalRepositoryBrowserGalleryPropertyModel()
    {
        parent :: __construct();

        foreach (MatterhornExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>