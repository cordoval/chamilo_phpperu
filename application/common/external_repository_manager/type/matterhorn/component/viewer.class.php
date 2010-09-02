<?php
class MatterhornExternalRepositoryManagerViewerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
         ExternalRepositoryComponent :: launch($this);
    }
}
?>