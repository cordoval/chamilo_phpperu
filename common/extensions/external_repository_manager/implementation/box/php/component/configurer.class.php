<?php
namespace common\extensions\external_repository_manager\implementation\box;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class BoxExternalRepositoryManagerConfigurerComponent extends BoxExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>