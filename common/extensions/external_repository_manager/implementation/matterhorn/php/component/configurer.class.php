<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class MatterhornExternalRepositoryManagerConfigurerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>