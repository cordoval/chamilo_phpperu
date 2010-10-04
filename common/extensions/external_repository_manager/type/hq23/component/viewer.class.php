<?php
class Hq23ExternalRepositoryManagerViewerComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>