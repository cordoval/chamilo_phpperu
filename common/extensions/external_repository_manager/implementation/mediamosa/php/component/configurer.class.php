<?php
class MediamosaExternalRepositoryManagerConfigurerComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
        //$slave_apps = ExternalRepositorySetting :: get('slave_app_ids', $this->get_external_repository_instance_id());

        ExternalRepositoryComponent :: launch($this);

 //       $new_slave_apps = ExternalRepositorySetting :: get('slave_app_ids', $this->get_external_repository_instance_id());
       
//        //update all assets if slave app_ids have changed - due to restrictions only up to 200 - maybe better iterate users
//        if($slave_apps != $new_slave_apps)
//        {
//            $this->get_external_repository_connector()->retrieve_external_repository_objects('', '', '', '', true);
//        }
    }
}
?>