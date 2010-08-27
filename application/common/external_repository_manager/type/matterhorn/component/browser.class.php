<?php
class MatterhornExternalRepositoryManagerBrowserComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
        $browser = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }
}
?>