<?php
class PicasaExternalRepositoryManagerViewerComponent extends PicasaExternalRepositoryManager
{

    function run()
    {
        $viewer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>