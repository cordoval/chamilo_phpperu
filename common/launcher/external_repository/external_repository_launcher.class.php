<?php
class ExternalRepositoryLauncher extends LauncherApplication
{
    const APPLICATION_NAME = 'external_repository';

    function ExternalRepositoryLauncher($user)
    {
        parent :: __construct($user);
    }

    function run()
    {
        $type = $this->get_type();
        $this->external_repository = RepositoryDataManager :: get_instance()->retrieve_external_repository($type);
        $this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, $type);
        
        ExternalRepositoryManager :: launch($this);
    }

    function get_type()
    {
        return Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
    }

    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_APPLICATION)
    {
        // Use this untill PHP 5.3 is available
    // Then use get_class($this) :: APPLICATION_NAME
    // and remove the get_application_name function();
    //$application = $this->get_application_name();
    //return Redirect :: get_link($application, $parameters, $filter, $encode_entities, $application_type);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    function get_external_repository()
    {
        return $this->external_repository;
    }
}
?>