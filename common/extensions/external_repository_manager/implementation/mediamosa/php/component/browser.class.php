<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

/**
 * Description of browserclass
 *
 * @author jevdheyd
 */

class MediamosaExternalRepositoryManagerBrowserComponent extends MediamosaExternalRepositoryManager
{

    function run()
    {
//        //select server if server_id = null
//        $server_selection_form = new MediamosaExternalRepositoryManagerServerSelectForm(MediamosaExternalRepositoryManagerServerSelectForm :: PARAM_SITUATION_BROWSE, $this);
//        $this->set_server_selection_form($server_selection_form);
//
//        if ($server_selection_form->validate())
//        {
//            $parameters = array();
//            $parameters[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_selection_form->get_selected_server();
//            $this->redirect('', false, $parameters);
//        }
//
//        if (! Request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER))
//        {
//            if ($server_selection_form->get_default_server())
//            {
//                $parameters = array();
//                $parameters[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $server_selection_form->get_default_server();
//                $this->redirect('', false, $parameters);
//
//            }
//        }

        ExternalRepositoryComponent :: launch($this);
    }
}
?>
