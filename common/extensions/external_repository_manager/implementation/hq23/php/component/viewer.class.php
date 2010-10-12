<?php
namespace common\extensions\external_repository_manager\implementation\hq23;
class Hq23ExternalRepositoryManagerViewerComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>