<?php
namespace common\extensions\external_repository_manager\implementation\picasa;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class PicasaExternalRepositoryManagerConfigurerComponent extends PicasaExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>