<?php
class ExternalRepositoryComponentImporterComponent extends ExternalRepositoryComponent
{

    function run()
    {
        $id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID);
        $object = $this->retrieve_external_repository_object($id);

        $succes = $this->import_external_repository_object($object);

        $params = array();
        //$params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY] = '';
        $params[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = '';

        if ($succes)
        {
            $this->redirect(Translation :: get('Succes'), false, $params);
        }
        else
        {
            $this->redirect(Translation :: get('Failed'), true, $params);
        }
    }
}
?>