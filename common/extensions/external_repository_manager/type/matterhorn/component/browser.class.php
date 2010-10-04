<?php
class MatterhornExternalRepositoryManagerBrowserComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
   		ExternalRepositoryComponent :: launch($this);
    }
}
?>