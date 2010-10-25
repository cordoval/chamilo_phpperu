<?php
namespace common\extensions\external_repository_manager\implementation\picasa;
class PicasaExternalRepositoryManagerBrowserComponent extends PicasaExternalRepositoryManager
{

    function run()
    {        
        ExternalRepositoryComponent :: launch($this);
    }
}
?>