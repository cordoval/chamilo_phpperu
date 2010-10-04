<?php
class FlickrExternalRepositoryManagerConfigurerComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>