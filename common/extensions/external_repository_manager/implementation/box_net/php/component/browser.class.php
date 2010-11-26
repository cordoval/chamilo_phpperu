<?php
namespace common\extensions\external_repository_manager\implementation\box_net;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class BoxNetExternalRepositoryManagerBrowserComponent extends BoxNetExternalRepositoryManager
{

    function run()
    {        
        ExternalRepositoryComponent :: launch($this);
    }
}
?>