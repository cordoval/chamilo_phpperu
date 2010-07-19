<?php
class FlickrExternalRepositoryManagerConfigurerComponent extends FlickrExternalRepositoryManager
{

    function run()
    {
        $configurer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: CONFIGURER_COMPONENT, $this);
        $configurer->run();
    }
}
?>