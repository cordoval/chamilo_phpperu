<?php
class YoutubeExternalRepositoryManagerConfigurerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        $configurer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: CONFIGURER_COMPONENT, $this);
        $configurer->run();
    }
}
?>