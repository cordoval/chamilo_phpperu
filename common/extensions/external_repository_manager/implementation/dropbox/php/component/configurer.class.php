<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;

class DropboxExternalRepositoryManagerConfigurerComponent extends DropboxExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>