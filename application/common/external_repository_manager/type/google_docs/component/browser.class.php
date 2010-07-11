<?php
class GoogleDocsExternalRepositoryManagerBrowserComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        $browser = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }
}
?>