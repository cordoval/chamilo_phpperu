<?php
class MediamosaExternalRepositoryManagerConfigurerComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
        $configurer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: CONFIGURER_COMPONENT, $this);
        $configurer->run();
    }
}
?>