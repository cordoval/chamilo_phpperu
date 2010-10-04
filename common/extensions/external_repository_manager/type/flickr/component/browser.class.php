<?php
class FlickrExternalRepositoryManagerBrowserComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>