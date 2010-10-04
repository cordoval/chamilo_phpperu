<?php
class Hq23ExternalRepositoryManagerConfigurerComponent extends Hq23ExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>