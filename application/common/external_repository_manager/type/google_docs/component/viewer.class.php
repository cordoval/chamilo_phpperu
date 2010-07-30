<?php
class GoogleDocsExternalRepositoryManagerViewerComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        $viewer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: VIEWER_COMPONENT, $this);
        $viewer->run();
    }
}
?>