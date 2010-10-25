<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class YoutubeExternalRepositoryManagerConfigurerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>