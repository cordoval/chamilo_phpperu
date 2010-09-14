<?php
class DropIoExternalRepositoryManagerConfigurerComponent extends DropIoExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>