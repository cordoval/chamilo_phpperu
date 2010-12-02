<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries;

use common\libraries\Request;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Redirect;
use common\libraries\Path;

use common\extensions\video_conferencing_manager\VideoConferencingComponent;
use repository\content_object\bbb_meeting\BbbMeeting;
use repository\ContentObjectTypeSelector;
use repository\RepositoryManager;

require_once dirname(__FILE__) . '/../forms/bbb_video_conferencing_manager_form.class.php';

class BbbVideoConferencingManagerCreatorComponent extends BbbVideoConferencingManager
{

    function run()
    {
        $this->display_header();
        $this->display_warning_message(Translation :: get('CreateViaRepository'));
  		$parameters = array();
    	$parameters[Application :: PARAM_APPLICATION] = RepositoryManager :: APPLICATION_NAME; 
    	$parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_CREATE_CONTENT_OBJECTS;
    	$parameters[ContentObjectTypeSelector :: PARAM_CONTENT_OBJECT_TYPE] = BbbMeeting :: get_type_name();
    	$link = Redirect :: get_link(RepositoryManager::APPLICATION_NAME, $parameters, array(), false, Redirect :: TYPE_CORE);
    	
        echo '<div style="text-align:center; margin:30px 0px 20px 0px;"><a class="button normal_button create_button" href="' . $link . '"> ' . Translation :: get('CreateMeeting') . '</a></div>';
          
        $this->display_footer();
        
    //        $form = new BbbVideoConferencingManagerForm(BbbVideoConferencingManagerForm :: TYPE_CREATE, $this->get_url(), $this);
    //        $object = new BbbVideoConferencingObject();
    //        $form->set_video_conferencing_object($object);
    //        
    //        if ($form->validate())
    //        {
    //            $result = $form->create_meeting();
    //
    //            if ($result instanceof BbbMeeting)
    //            {
    //            	$parameters = array();
    //                $parameters[Application :: PARAM_APPLICATION] = RepositoryManager :: APPLICATION_NAME;
    //                $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS;
    //                $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_ID] = $result->get_id();
    //
    //                Redirect :: web_link(Path :: get(WEB_PATH) . 'core.php', $parameters);
    //            
    //            }
    //            else
    //            {
    //                Request :: set_get(Application :: PARAM_ERROR_MESSAGE, $result);
    //                $this->display_header();
    //                $form->display();
    //                $this->display_footer();
    //            }
    //        }
    //        else
    //        {
    //            $this->display_header();
    //            $form->display();
    //            $this->display_footer();
    //        }
    }
}
?>