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
        //select server if server_id = null
        $server_selection_form = new MediamosaStreamingMediaManagerServerSelectForm(MediamosaStreamingMediaManagerServerSelectForm :: PARAM_SITUATION_UPLOAD, $this);
        $this->set_server_selection_form($server_selection_form);

        if($server_selection_form->validate())
        {
            $parameters = array();
            $parameters[MediamosaStreamingMediaManager :: PARAM_SERVER] = $server_selection_form->get_selected_server();
            $this->redirect(Translation :: get('Server_selected'), false, $parameters);
        }
        
        if(!Request :: get(MediamosaStreamingMediaManager :: PARAM_SERVER))
        {
            if($server_selection_form->get_default_server())
            {
                $parameters = array();
                $parameters[MediamosaStreamingMediaManager :: PARAM_SERVER] = $server_selection_form->get_default_server();
                $this->redirect('', false, $parameters);

            }
        }

        if(Request :: get(MediamosaStreamingMediaManager :: PARAM_SERVER))
        {
            //check quota if user exists
            //otherwise create one and set quota
            $connector = MediamosaStreamingMediaConnector :: get_instance();
            $user = Session :: get_user_id();
            $over_quota = true;

            if($mediamosa_user = $connector->retrieve_mediamosa_user($user))
            {
                if((string) $mediamosa_user->user_over_quota == 'false')
                {
                    $over_quota = false;
                }
            }
            else
            {
                $mdm = MediamosaStreamingMediaDataManager :: get_instance();
                if($quotum = $mdm->retrieve_streaming_media_user_quotum($user, Request :: get(MediamosaStreamingMediaManager :: PARAM_SERVER)))
                {
                    if($connector->set_mediamosa_user_quotum($user, $quotum->get_quotum()))
                    {
                        $over_quota = false;
                    }
                }
                else
                {
                    if($connector->set_mediamosa_default_user_quotum($user))
                    {
                        $over_quota = false;
                    }
                }
            }
            
            if(!$over_quota)
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
                       $uploadform = new MediamosaStreamingMediaManagerUploadForm($ticket_response['action'], $redirect_url, $ticket_response['uploadprogress_url'], $this);

                       $this->display_header($trail, false);
                       $uploadform->display();
                       $this->display_footer();
                   }
                   else
                   {
                       $this->display_header($trail, false);
                       //TODO: jens redirect??
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
            else
            {
                 $this->display_header($trail, false);
                 echo Translation::get('OverQuota');
                 $this->display_footer();
            }
            
        }else
        {
            $this->display_header($trail, false);
            $this->display_footer();
        }
       
        
    } 

}
?>
