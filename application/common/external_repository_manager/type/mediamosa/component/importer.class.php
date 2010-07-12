<?php
/**
 * Description of importerclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerImporterComponent extends MediamosaExternalRepositoryManager{

    function run()
    {
        /*//select server if server_id = null
        $server_selection_form = new MediamosaExternalRepositoryManagerServerSelectForm(MediamosaExternalRepositoryManagerServerSelectForm :: PARAM_SITUATION_BROWSE, $this);
        $this->set_server_selection_form($server_selection_form);

        if($server_selection_form->validate())
        {
            $parameters = array();
            $parameters[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_selection_form->get_selected_server();
            $this->redirect(Translation :: get('Server_selected'), false, $parameters);
        }
        */
        if(request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER))
        {
            $importer = ExternalRepositoryComponent::factory(ExternalRepositoryComponent::IMPORTER_COMPONENT, $this);
            $importer->run();
        }
    }

}
?>
