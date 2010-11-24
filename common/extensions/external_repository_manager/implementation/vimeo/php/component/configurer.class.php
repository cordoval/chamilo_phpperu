<?php
namespace common\extensions\external_repository_manager\implementation\vimeo;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class VimeoExternalRepositoryManagerConfigurerComponent extends VimeoExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>