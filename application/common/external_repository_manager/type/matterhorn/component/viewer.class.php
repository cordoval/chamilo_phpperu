<?php
class MatterhornExternalRepositoryManagerViewerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
        $viewer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>