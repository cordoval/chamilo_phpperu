<?php
class YoutubeExternalRepositoryManagerBrowserComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        $browser = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }
}
?>