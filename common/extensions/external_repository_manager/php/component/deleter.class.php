<?php
namespace common\extensions\external_repository_manager;

use common\libraries\Request;

class ExternalRepositoryComponentDeleterComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->delete_external_repository_object($id);
    }
}
?>
