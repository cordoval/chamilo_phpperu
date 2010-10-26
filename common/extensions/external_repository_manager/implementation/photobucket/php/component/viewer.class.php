<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class PhotobucketExternalRepositoryManagerViewerComponent extends PhotobucketExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>