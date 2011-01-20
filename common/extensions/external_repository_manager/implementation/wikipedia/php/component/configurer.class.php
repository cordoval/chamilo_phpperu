<?php
namespace common\extensions\external_repository_manager\implementation\wikipedia;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class WikipediaExternalRepositoryManagerConfigurerComponent extends WikipediaExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>