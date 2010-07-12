<?php
class FlickrExternalRepositoryManagerBrowserComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        $browser = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }
}
?>