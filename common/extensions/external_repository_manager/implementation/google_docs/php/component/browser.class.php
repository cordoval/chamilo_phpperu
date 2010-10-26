<?php
namespace common\extensions\external_repository_manager\implementation\google_docs;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class GoogleDocsExternalRepositoryManagerBrowserComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>