<?php
class GoogleDocsExternalRepositoryManagerViewerComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>