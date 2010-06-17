<?php
/**
 * Description of uploaderclass
 *
 * @author jevdheyd
 */

require_once(dirname(__FILE__).'/../forms/mediamosa_streaming_media_manager_form.class.php');
require_once(dirname(__FILE__).'/../forms/mediamosa_streaming_media_manager_upload_form.class.php');

class MediamosaStreamingMediaManagerUploaderComponent extends MediamosaStreamingMediaManager {

    function run()
    {

        $form = new MediamosaStreamingMediaManagerForm(MediamosaStreamingMediaManagerForm :: TYPE_CREATE, $this->get_url(), $this);
        if($form->validate())
        {
           //if - create necessary objects and upload metadata
           if($ticket_response = $form->prepare_upload())
           {
               $params = array();
               $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager :: ACTION_VIEW_STREAMING_MEDIA;
               $params[StreamingMediaManager :: PARAM_TYPE] = 'mediamosa';
               $params[StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID] = $ticket_response['asset_id'];

               $redirect_url = 'http://' . $_SERVER['SERVER_NAME'] . $this->get_url($params, true);

               //generate uploadform
               $uploadform = new MediamosaStreamingMediaManagerUploadForm($ticket_response['action'], $redirect_url, $ticket_response['uploadprogress_url']);
               
               $this->display_header($trail, false);
               $uploadform->display();
               $this->display_footer();
           }
           else
           {
               $this->display_header($trail, false);
               echo Translation :: get('failed');
               $this->display_footer();
           }
        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
    }

}
?>
