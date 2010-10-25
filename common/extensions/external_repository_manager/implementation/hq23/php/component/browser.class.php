<?php
namespace common\extensions\external_repository_manager\implementation\hq23;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class Hq23ExternalRepositoryManagerBrowserComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>