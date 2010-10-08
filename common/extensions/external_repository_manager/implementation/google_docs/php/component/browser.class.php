<?php
class GoogleDocsExternalRepositoryManagerBrowserComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>