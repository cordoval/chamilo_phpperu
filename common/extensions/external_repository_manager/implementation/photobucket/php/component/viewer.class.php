<?php
namespace common\extensions\external_repository_manager\implementation\photobucket;
class PhotobucketExternalRepositoryManagerViewerComponent extends PhotobucketExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>