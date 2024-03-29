<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\Redirect;
use common\libraries\Path;

use common\extensions\external_repository_manager\ExternalRepositoryManager;

require_once dirname(__FILE__) . '/../forms/youtube_external_repository_manager_form.class.php';
require_once dirname(__FILE__) . '/../forms/youtube_external_repository_manager_upload_form.class.php';

class YoutubeExternalRepositoryManagerUploaderComponent extends YoutubeExternalRepositoryManager
{

    function run()
    {
        $form = new YoutubeExternalRepositoryManagerForm(YoutubeExternalRepositoryManagerForm :: TYPE_CREATE, $this->get_url(), $this);
        
        if ($form->validate())
        {
            $upload_token = $form->get_upload_token();
            
            if ($upload_token)
            {
                $parameters = $this->get_parameters();
                $parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
                $parameters[YoutubeExternalRepositoryManager :: PARAM_FEED_TYPE] = YoutubeExternalRepositoryManager :: FEED_TYPE_MYVIDEOS;
                
                if ($this->is_stand_alone())
                {
                    $platform_url = Redirect :: get_web_link(Path :: get(WEB_PATH) . 'common/launcher/index.php', $parameters);
                }
                else
                {
                    $platform_url = Redirect :: get_web_link(Path :: get(WEB_PATH) . 'core.php', $parameters);
                }
                
                $next_url = $upload_token['url'] . '?nexturl=' . urlencode($platform_url);
                $form = new YoutubeExternalRepositoryManagerUploadForm($next_url, $upload_token['token']);
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