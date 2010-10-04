<?php
class YoutubeExternalRepositoryManagerViewerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>