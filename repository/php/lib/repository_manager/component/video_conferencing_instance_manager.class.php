<?php
namespace repository;

use common\libraries\Path;

require_once Path :: get_repository_path() . 'lib/video_conferencing_instance_manager/video_conferencing_instance_manager.class.php';

class RepositoryManagerVideoConferencingInstanceManagerComponent extends RepositoryManager
{

    function run()
    {
        VideoConferencingInstanceManager :: launch($this);
    }
}
?>