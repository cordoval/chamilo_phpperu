<?php
require_once dirname(__FILE__) . '/../forms/youtube_streaming_media_manager_form.class.php';
class YoutubeStreamingMediaManagerEditorComponent extends YoutubeStreamingMediaManager
{

    function run()
    {
    	$id = Request :: get(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID);
        $form = new YoutubeStreamingMediaManagerForm(YoutubeStreamingMediaManagerForm :: TYPE_EDIT, $this->get_url(array(StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID => $id)), $this);
        
        
        $object = $this->retrieve_streaming_media_object($id);
        
        $form->set_streaming_media_object($object);
        
        if ($form->validate())
        {
            $success = $form->update_video_entry();
            
            $parameters = $this->get_parameters();
            $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager :: ACTION_VIEW_STREAMING_MEDIA;
            $parameters[StreamingMediaManager :: PARAM_STREAMING_MEDIA_ID] = $object->get_id();   
            	
            if ($this->is_stand_alone())
            {
            	Redirect :: web_link(Path :: get(WEB_PATH) . 'common/launcher/index.php', $parameters);
            }
            else
            {
                Redirect :: web_link(Path :: get(WEB_PATH) . 'core.php', $parameters);
            }
        
        //$this->redirect(Translation :: get($success ? 'ObjectUpdated' : 'ObjectUpdateFailed'), ($success ? false : true), $parameters);

        }
        else
        {
            $this->display_header($trail, false);
            $form->display();
            $this->display_footer();
        }
        
    //$object = $this->retrieve_streaming_media_object($id);
    //dump($object);
    }
}
?>