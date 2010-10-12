<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;
class MatterhornExternalRepositoryManagerViewerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
         ExternalRepositoryComponent :: launch($this);
    }
}
?>