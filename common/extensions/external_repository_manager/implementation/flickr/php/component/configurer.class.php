<?php
namespace common\extensions\external_repository_manager\implementation\flickr;
use \ExternalRepositoryComponent;
class FlickrExternalRepositoryManagerConfigurerComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>