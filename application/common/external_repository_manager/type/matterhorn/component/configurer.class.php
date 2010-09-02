<?php
class MatterhornExternalRepositoryManagerConfigurerComponent extends MatterhornExternalRepositoryManager
{

    function run()
    {
        $configurer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: CONFIGURER_COMPONENT, $this);
        $configurer->run();
    }
}
?>