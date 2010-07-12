<?php
class YoutubeExternalRepositoryManagerViewerComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        $viewer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>