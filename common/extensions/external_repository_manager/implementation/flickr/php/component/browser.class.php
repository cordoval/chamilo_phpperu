<?php
namespace common\extensions\external_repository_manager\implementation\flickr;
use \ExternalRepositoryComponent;
class FlickrExternalRepositoryManagerBrowserComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>