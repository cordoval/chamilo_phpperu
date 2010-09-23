<?php
class Hq23ExternalRepositoryManagerBrowserComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>