<?php
class PicasaExternalRepositoryManagerBrowserComponent extends PicasaExternalRepositoryManager
{

    function run()
    {        
        ExternalRepositoryComponent :: launch($this);
    }
}
?>