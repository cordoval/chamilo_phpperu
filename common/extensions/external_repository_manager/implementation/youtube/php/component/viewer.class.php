<?php
namespace common\extensions\external_repository_manager\implementation\youtube;
class YoutubeExternalRepositoryManagerViewerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>