<?php
namespace common\extensions\external_repository_manager\implementation\matterhorn;
class MatterhornExternalRepositoryManagerConfigurerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>