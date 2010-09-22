<?php
class PhotobucketExternalRepositoryManagerConfigurerComponent extends PhotobucketExternalRepositoryManager
{

    function run()
    {
        ExternalRepositoryComponent :: launch($this);
    }
}
?>