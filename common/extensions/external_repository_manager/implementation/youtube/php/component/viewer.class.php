<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class YoutubeExternalRepositoryManagerViewerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>