<?php
/**
 * Description of importerclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerImporterComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
        $server_selection_form = new MediamosaExternalRepositoryManagerServerSelectForm(MediamosaExternalRepositoryManagerServerSelectForm :: PARAM_SITUATION_BROWSE, $this);
        $parameters[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_selection_form->get_selected_server();
        if (request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER))
        {
            $importer = ExternalRepositoryComponent :: factory(ExternalRepositoryComponent :: IMPORTER_COMPONENT, $this);
            $importer->run();
        }
    }

}
?>
