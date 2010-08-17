<?php
class PicasaExternalRepositoryManagerBrowserComponent extends PicasaExternalRepositoryManager
{

    function run()
    {        
        $browser = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: BROWSER_COMPONENT, $this);
        $browser->run();
    }
}
?>