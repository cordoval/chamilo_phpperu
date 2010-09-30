<?php
class PhotobucketExternalRepositoryManagerViewerComponent extends PhotobucketExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>