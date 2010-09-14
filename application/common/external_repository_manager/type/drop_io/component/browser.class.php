<?php
class DropIoExternalRepositoryManagerBrowserComponent extends DropIoExternalRepositoryManager
{

    function run()
    {        
    	ExternalRepositoryComponent :: launch($this);
    }
}
?>