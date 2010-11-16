<?php
namespace common\extensions\external_repository_manager\implementation\dropbox;

class DropboxExternalRepositoryManagerConfigurerComponent extends DropboxExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>