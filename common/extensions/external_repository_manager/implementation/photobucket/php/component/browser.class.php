<?php
class PhotobucketExternalRepositoryManagerBrowserComponent extends PhotobucketExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>