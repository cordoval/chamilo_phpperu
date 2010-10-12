<?php
namespace common\extensions\external_repository_manager\implementation\youtube;
class YoutubeExternalRepositoryManagerConfigurerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>