<?php
class FlickrExternalRepositoryManagerViewerComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        $viewer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>