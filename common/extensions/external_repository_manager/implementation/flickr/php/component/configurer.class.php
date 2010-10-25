<?php
namespace common\extensions\external_repository_manager\implementation\flickr;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class FlickrExternalRepositoryManagerConfigurerComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>