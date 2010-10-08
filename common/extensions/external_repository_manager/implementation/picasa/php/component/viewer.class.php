<?php
class PicasaExternalRepositoryManagerViewerComponent extends PicasaExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>