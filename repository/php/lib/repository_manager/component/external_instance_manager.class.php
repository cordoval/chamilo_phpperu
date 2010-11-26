<?php
namespace repository;

use common\libraries\Path;

require_once Path :: get_repository_path() . 'lib/external_instance_manager/external_instance_manager.class.php';

class RepositoryManagerExternalInstanceManagerComponent extends RepositoryManager
{

    function run()
    {
        ExternalInstanceManager :: launch($this);
    }
}
?>