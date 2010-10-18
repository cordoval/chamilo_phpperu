<?php
namespace common\extensions\external_repository_manager\implementation\google_docs;
class GoogleDocsExternalRepositoryManagerBrowserComponent extends GoogleDocsExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>