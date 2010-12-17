<?php
namespace common\extensions\external_repository_manager\implementation\slideshare;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class SlideshareExternalRepositoryManagerConfigurerComponent extends SlideshareExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>