<?php
class MatterhornExternalRepositoryManagerConfigurerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>