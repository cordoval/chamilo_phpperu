<?php
require_once dirname(__FILE__) . '/../forms/youtube_streaming_media_manager_form.class.php';
class YoutubeStreamingMediaManagerEditorComponent extends YoutubeStreamingMediaManager
{
	function run()
	{
		$form = new YoutubeStreamingMediaManagerForm(YoutubeStreamingMediaManagerForm :: TYPE_EDIT, $this->get_url(), $this);
		
		
		if ($form->validate())
        {
            $upload_token = $form->get_upload_token();

            if ($upload_token)
            {
                $platform_url = Redirect :: web_link(PATH :: get(WEB_PATH) . '/application/common/streaming_media_manager/index.php', $this->get_parameters());
            	$next_url = $upload_token['url'] .'?nexturl=' . $platform_url;
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
        
		//$object = $this->retrieve_streaming_media_object($id);
		//dump($object);
	}
}
?>