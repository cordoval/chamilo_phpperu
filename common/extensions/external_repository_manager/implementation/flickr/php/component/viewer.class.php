<?php
class FlickrExternalRepositoryManagerViewerComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>