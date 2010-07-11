<?php
/**
 * Description of importerclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaManagerImporterComponent extends MediamosaStreamingMediaManager{

    function run()
    {
        /*//select server if server_id = null
        $server_selection_form = new MediamosaStreamingMediaManagerServerSelectForm(MediamosaStreamingMediaManagerServerSelectForm :: PARAM_SITUATION_BROWSE, $this);
        $this->set_server_selection_form($server_selection_form);

        if($server_selection_form->validate())
        {
            $parameters = array();
            $parameters[MediamosaStreamingMediaManager :: PARAM_SERVER] = $server_selection_form->get_selected_server();
            $this->redirect(Translation :: get('Server_selected'), false, $parameters);
        }
        */
        if(request :: get(MediamosaStreamingMediaManager :: PARAM_SERVER))
        {
            $importer = StreamingMediaComponent::factory(StreamingMediaComponent::IMPORTER_COMPONENT, $this);
            $importer->run();
        }
    }

}
?>
