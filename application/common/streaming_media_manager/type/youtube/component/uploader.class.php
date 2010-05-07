<?php
require_once dirname(__FILE__) . '/../forms/youtube_streaming_media_manager_form.class.php';
require_once dirname(__FILE__) . '/../forms/youtube_streaming_media_manager_upload_form.class.php';

class YoutubeStreamingMediaManagerUploaderComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$form = new YoutubeStreamingMediaManagerForm(YoutubeStreamingMediaManagerForm :: TYPE_CREATE, $this->get_url(), $this);
		
		if ($form->validate())
        {
            $upload_token = $form->get_upload_token();

            if ($upload_token)
            {
           		$parameters = $this->get_parameters();
                $parameters[StreamingMediaManager::PARAM_STREAMING_MEDIA_MANAGER_ACTION] = StreamingMediaManager::ACTION_BROWSE_STREAMING_MEDIA;
                $parameters[YoutubeStreamingMediaManager::PARAM_FEED_TYPE] = YoutubeStreamingMediaManager::FEED_TYPE_MYVIDEOS;
                
            	if ($this->is_stand_alone())
                {
                	$platform_url = Redirect :: get_web_link(PATH :: get(WEB_PATH) . 'common/launcher/index.php', $parameters);
                }
                else
                {
                	$platform_url = Redirect :: get_web_link(PATH :: get(WEB_PATH) . 'core.php', $parameters);
                }

            	$next_url = $upload_token['url'] .'?nexturl=' . urlencode($platform_url);
            	$form = new YoutubeStreamingMediaManagerUploadForm($next_url, $upload_token['token']);
            	$this->display_header($trail, false);
            	$form->display();
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