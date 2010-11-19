<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class SoundcloudExternalRepositoryManagerBrowserComponent extends SoundcloudExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>