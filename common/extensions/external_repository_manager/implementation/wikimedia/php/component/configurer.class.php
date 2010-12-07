<?php
namespace common\extensions\external_repository_manager\implementation\wikimedia;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class WikimediaExternalRepositoryManagerConfigurerComponent extends WikimediaExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>