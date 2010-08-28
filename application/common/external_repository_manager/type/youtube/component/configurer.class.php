<?php
class YoutubeExternalRepositoryManagerConfigurerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>