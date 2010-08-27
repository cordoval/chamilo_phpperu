<?php
require_once Path :: get_repository_path() . 'lib/external_repository_instance_manager/external_repository_instance_manager.class.php';

class RepositoryManagerExternalRepositoryInstanceManagerComponent extends RepositoryManager
{

    function run()
    {
        ExternalRepositoryInstanceManager::launch($this);
//        $external_repository_instance_manager = new ExternalRepositoryInstanceManager($this);
//        $external_repository_instance_manager->run();
    }
}
?>